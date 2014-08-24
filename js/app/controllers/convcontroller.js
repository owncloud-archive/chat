/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
Chat.angular.controller('ConvController', ['$scope', '$http', '$filter', '$interval', function($scope, $http, $filter, $interval) {
	$scope.convs = {};
	$scope.contacts = [];
	$scope.contactsList = [];
	$scope.backends = [];
	$scope.initConvs = {};
	$scope.active = {
		backend : {},
		conv : {},
		window : true,
		//user :
	};
	// $scope.userToInvite // generated in main.php // this is the user to invite in the new conv panel
	$scope.title = {};
	$scope.title.title = "";
	$scope.title.default = "Chat - ownCloud";
	$scope.title.new_msgs = [];
	$scope.debug = [];
	$scope.fields = {
		'chatMsg' : '',
	};
	
	Chat.scope = angular.element($("#app")).scope();
	var initvar = JSON.parse($('#initvar').text());
	$scope.contacts = initvar.contacts;
	$scope.contactsList = initvar.contactsList;
	$scope.contactsObj = initvar.contactsObj;
	$scope.backends = initvar.backends;
	$scope.active.user = $scope.contactsObj[OC.currentUser];
	$scope.initConvs = initvar.initConvs;
	$scope.initvar = initvar;
	for (var active in $scope.backends) break;
	$scope.active.backend =  $scope.backends[active];
	for(var namespace in $scope.backends){
		var backend = $scope.backends[namespace];
		if(namespace === 'och'){
			Chat[namespace].util.init();
		}
	}
	$scope.initDone = true;


	$scope.quit = function(){
		for(var namespace in $scope.backends){
			var backend = $scope.backends[namespace];
			if(namespace === 'och'){
				Chat[namespace].util.quit();
			}
		}
	};

	$scope.selectBackend = function(backend){
		$scope.active.backend = backend;
	};

	$scope.view = {
		elements : {
			"emptyMsg" : true,
			"chat" : false,
			"initDone" : false,
			"settings" : false,
			"emojiContainer" : false,
			"invite" : false,
		},
		inviteClick : function(){
			$scope.view.toggle('invite');
			// Focus search field
			setTimeout(function(){
				$('#invite-search-field').focus();
			}, 1);
		},
		showEmojiPopover : function(){
			var height = $("#chat-window-footer").height();
			$scope.view.toggle('emojiContainer');
			setTimeout(function(){
				$('#emoji-container').css('bottom', height + 20);
			},1);
			console.log(height);
		},
		show : function(element, $event, exception){
			if($event !== undefined){
				var classList = $event.target.classList;
				if(classList.contains(exception)){
					// the clicked item containted the exception class
					// this mean probably that we clicked on the item in side the viewed div
					// thus the div don't need to be hided;
					return;
				}
			}
			$scope.view.elements[element] = true;
		},
		hide : function(element, $event, exceptions){
			if($event !== undefined){
				var classList = $event.target.classList;
				for(var i = 0; i < exceptions.length; i++){
					if(classList.contains(exceptions[i])){
						// the clicked item containted the exception class
						// this mean probably that we clicked on the item in side the viewed div
						// thus the div don't need to be hided;
						return;
					}
				}
			}
			$scope.view.elements[element] = false;
		},
		toggle : function(element, $event){
			$scope.view.elements[element] = !$scope.view.elements[element];
			if ($event !== undefined) {
				if (typeof $event.stopPropagation === "function") {
				$event.stopPropagation();
				}
			}
		},
		updateTitle : function(newTitle){
			$scope.title = newTitle;
		},
		makeActive : function(convId, $event, exception){
			$scope.view.hide('emptyMsg');
			$scope.view.show('chat', $event, exception);
			$scope.active.conv = convId;
			$scope.view.focusMsgInput();
			$scope.convs[convId].new_msg = false;
			$("#chat-msg-input-field").autosize({
				callback : function(){
					var height = $("#chat-msg-input-field").height();
					height = height + 15;
					$('#chat-window-footer').height(height);
					$('#chat-window-body').css('bottom', height);
					$('#chat-window-msgs').scrollTop($('#chat-window-msgs')[0].scrollHeight);
					var height = $("#chat-window-footer").height();
					$('#emoji-container').css('bottom', height + 20);
				}
			});
		},
		unActive : function(){
			$scope.active.conv = null;
		},
		addConv : function(convId, users, backend, msgs){
			// generate conv name + higher order of contacts
			var name  = '';
			for(var key in users){
				var user = users[key];
				if(user.id !== Chat.scope.active.user.id){
					name += user.displayname + ' ';
					var order = $scope.getHighestOrderContacts();
					$scope.contactsObj[user.id].order = order;
				}
			}
			// end generate conv name


			if($scope.convs[convId] === undefined) {
				// get highest order

				var order = $scope.getHighestOrder();

				$scope.convs[convId] = {
					id : convId,
					users : users,
					msgs : [],
					backend : backend,
					new_msg : false,
					raw_msgs : [],
					order : order,
					name : name
				};
				$scope.view.makeActive(convId);
				if(msgs !== undefined){
					for (var key in msgs){
						var msg = msgs[key];
						$scope.view.addChatMsg(convId, Chat.scope.contactsObj[msg.user], msg.msg, msg.timestamp, backend);
					}
				}
			}
		},
		addChatMsg : function(convId, user, msg, timestamp, backend, noNotify){
			if(user.id !== $scope.active.user.id){
				$scope.notify(user.displayname);
			}

			if(noNotify === undefined){
				var noNotify = false;
			}

			if(convId !== $scope.active.conv && noNotify === false){
				// this ins't the active conv
				// we have to notify the user of new messages in this conv
				$scope.view.notifyMsgInConv(convId);
			}

			// Check if the user is equal to the user of the last msg
			// First get the last msg
			var contact = user;

			if($scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1] !== undefined){
				var lastMsg = $scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1];
				if(lastMsg.contact.displayname === contact.displayname){
					// The current user send the last message 
					// so don't readd the border etc
					
					if(Chat.app.util.isYoutubeUrl(lastMsg.msg) || Chat.app.util.isImageUrl(lastMsg.msg)){
						if (Chat.app.util.timeStampToDate(lastMsg.timestamp).minutes === Chat.app.util.timeStampToDate(timestamp).minutes
							&& Chat.app.util.timeStampToDate(lastMsg.timestamp).hours === Chat.app.util.timeStampToDate(timestamp).hours
							){
							$scope.convs[convId].msgs.push({
								contact : contact,
								msg : $.trim(msg),
								timestamp : timestamp,
								time : null,
							});
						} else {
							$scope.convs[convId].msgs.push({
								contact : contact,
								msg : $.trim(msg),
								timestamp : timestamp,
								time : Chat.app.util.timeStampToDate(timestamp),
							});
						}
					} else {
						lastMsg.msg = lastMsg.msg + "\n" + msg;
						$scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1] = lastMsg;
					}
				} else if (Chat.app.util.timeStampToDate(lastMsg.timestamp).minutes === Chat.app.util.timeStampToDate(timestamp).minutes
							&& Chat.app.util.timeStampToDate(lastMsg.timestamp).hours === Chat.app.util.timeStampToDate(timestamp).hours
							) {
					$scope.convs[convId].msgs.push({
						contact : contact,
						msg : $.trim(msg),
						timestamp : timestamp,
						time : null,
					});
				} else {
					$scope.convs[convId].msgs.push({
						contact : contact,
						msg : $.trim(msg),
						timestamp : timestamp,
						time : Chat.app.util.timeStampToDate(timestamp),
					});
				}
			} else {
				$scope.convs[convId].msgs.push({
					contact : contact,
					msg : $.trim(msg),
					timestamp : timestamp,
					time : Chat.app.util.timeStampToDate(timestamp),
				});
			}

			// Add raw msgs to raw_msgs
			$scope.convs[convId].raw_msgs.push({"msg" : msg, "timestamp" : timestamp, "user" : user});
			$scope.convs[convId].order = $scope.getHighestOrder() +1;
		},
		addUserToConv : function(convId, user){
			if($scope.convs[convId].users.indexOf(user) === -1){
				$scope.convs[convId].users.push(user);
			}
		},
		focusMsgInput : function(){
			$('#chat-msg-input-field');
		},
		replaceUsers : function(convId, users){
			$scope.convs[convId].users = users;
		},
		notifyMsgInConv : function(convId){
			$scope.convs[convId].new_msg = true;
		},
		makeUserOnline : function(userId){
			$scope.contactsObj[userId].online = true;
		},
		makeUserOffline : function(userId){
			$scope.contactsObj[userId].online = false;
		}
	};


	$scope.sendChatMsg = function(){
		if ($scope.fields.chatMsg !== ''){
			var backend = $scope.convs[$scope.active.conv].backend.name;
			$scope.view.addChatMsg($scope.active.conv, $scope.active.user, $scope.fields.chatMsg, Time.now(), backend);
			Chat[backend].on.sendChatMsg($scope.active.conv, $scope.fields.chatMsg);
			$scope.debug.push($scope.fields.chatMsg);
			$scope.fields.chatMsg = '';
			var order = $scope.getHighestOrderContacts();
			setTimeout(function(){
				$('#chat-msg-input-field').trigger('autosize.resize');
			},1);
			$('#chat-msg-input-field').focus();

			for (var key in $scope.convs[$scope.active.conv].users) {
				var user =  $scope.convs[$scope.active.conv].users[key];
				if(user.id !== Chat.scope.active.user.id){
					var order = $scope.getHighestOrderContacts();
					$scope.contactsObj[user.id].order = order;
				}
			}
		}
	};

	$scope.makeFirstConvActive = function(){
		firstConv = $scope.getFirstConv();
		if(firstConv === undefined){
			$scope.active.conv = null;
			$scope.view.hide('chat');
			$scope.view.show('emptyMsg');
		} else {
			$scope.view.makeActive(firstConv);
		}
	};

	$scope.getFirstConv = function(){
		for (firstConv in $scope.convs) break;
		if (typeof firstConv !== 'undefined') {
			return firstConv;
		} else {
			return undefined;
		}
	}

	$scope.invite = function(userToInvite){
		var backend = $scope.convs[$scope.active.conv].backend.name;
		if(userToInvite !== $scope.active.user && userToInvite !== ''){
			Chat[backend].on.invite($scope.active.conv, userToInvite);
		} else {
			console.log('Tried to invite the active user or an empty user');
		}
		$scope.view.hide('invite');
		$scope.view.makeActive($scope.active.conv);

		var order = $scope.getHighestOrderContacts();
		$scope.contactsObj[userToInvite.id].order = order;

	};

	function updateContacts(){
		var url = OC.generateUrl('/apps/chat/contacts')
		$http.get('/index.php/apps/chat/contacts?requesttoken=' + oc_requesttoken)
			.success(function(data, status) {
				$scope.contacts = data.contacts;
				$scope.contactsObj = data.contactsObj;
			});
	};

//	$interval(updateContacts, 10000);
	$interval(function(){
		if($scope.title.title === ''){
			$('title').text($scope.title.default);
		} else {
			$('title').text($scope.title.title);
		}
	}, 1000);
	$interval(function(){
		$('title').text($scope.title.default);
	}, 2000);

	$scope.$watchCollection('title.new_msgs', function(){
		if($scope.active.window === false){
			var title = 'New messages from ';
			if($scope.title.new_msgs.length === 0 ){
				title = '';
			} else {
				for (var key in $scope.title.new_msgs){
					var user = $scope.title.new_msgs[key];
					title = title + user + " ";
				}
			}
			$scope.title.title = title;
		} else {
			$scope.title.tile = '';
		}
	});

	$scope.notify = function(user){
		if($scope.title.new_msgs.indexOf(user) == -1){
			$scope.title.new_msgs.push(user);
		}
	};

	window.onfocus = function () {
		$scope.title.title = '';
		$scope.title.new_msgs = [];
		$scope.active.window = true;
	};

	window.onblur = function () {
		$scope.active.window = false; 
	};

	$scope.addEmoji = function(name){
		var element = $("#chat-msg-input-field");
		element.focus(); //ie
		var selection = element.getSelection();
		var textBefore = $scope.fields.chatMsg.substr(0, selection.start);
		var textAfter = $scope.fields.chatMsg.substr(selection.end);
		$scope.fields.chatMsg = textBefore + ' ' + name + ' ' + textAfter + ' ';
		$scope.view.hide('emojiContaineradd');
	};

	$scope.emojis = Chat.app.util.emojis;

	$scope.$watch('convs[active.conv].msgs', function(){
		setTimeout(function(){
			$('#chat-window-msgs').scrollTop($('#chat-window-msgs')[0].scrollHeight);
		},250);
	}, true);

	$scope.getHighestOrder = function(){
		var sortedConvs = $filter('orderObjectBy')($scope.convs, 'order');
		if(sortedConvs[sortedConvs.length - 1] !== undefined){
			return sortedConvs[sortedConvs.length - 1].order + 1;
		} else {
			return 1;
		}
	};

	$scope.getHighestOrderContacts = function(){
		var sortedContacts = $filter('orderObjectBy')($scope.contactsObj, 'order');
		return sortedContacts[sortedContacts.length - 1].order + 1;
	};

}]).directive('avatar', function() {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			element.applyContactAvatar(attrs.addressbookBackend, attrs.addressbookId, attrs.id, attrs.displayname, attrs.size);
			if(attrs.online !== undefined){
				element.online(attrs.isonline);
				$scope.$watch('contactsObj', function(){
					element.online(Chat.scope.contactsObj[attrs.id].online);
				}, true);
			}
		}
	};
}).filter('userFilter', function() {
	return function(users) {
		var output = [];
		for (var key in users){
			var user = users[key];
			if(user.id !== Chat.scope.active.user.id){
				output.push(user);
			}
		}
		return output;
	};
}).directive('ngEnter', function () {
	return function (scope, element, attrs) {
		element.bind("keydown keypress", function (event) {
			if(event.which === 13) {
				if (event.shiftKey === false){
					scope.$apply(function (){
						scope.$eval(attrs.ngEnter);
					});
					event.preventDefault();
				}
			}
		});
	};
}).directive('tipsy', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			element.tipsy();
		}
	};
}).directive('displayname', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			var text  = '';
			if(typeof attrs.users === 'string' ){
				users = JSON.parse(attrs.users);
			} else {
				users = attrs.users;
			}
			for (var key in users){
				var user = users[key];
				if(user.id !== Chat.scope.active.user.id){
					text += user.displayname + ' ';
				}
			}
			element.text(text);
		}
	};
}).directive('autoHeight', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			if(attrs.convId === $scope.active.conv){
				attrs.itemCount++;
			} else {
				element.css('height', attrs.minHeight + 'px');
			}
			var height = attrs.itemCount * attrs.itemHeight;
			if(height < attrs.minHeight){
				element.css('height', attrs.minHeight + 'px');
			} else {
				element.css('height', height + 'px');
			}
			$scope.$watch('active.conv', function(){
				if(attrs.convId === $scope.active.conv){
					var height = attrs.itemCount * attrs.itemHeight;
					if(attrs.itemCount > 1){
						height = height + 30;
					}
					if(height < attrs.minHeight){
						element.css('height', attrs.minHeight + 'px');
					} else {
						element.css('height', height + 'px');
					}
				} else {
					element.css('height', attrs.minHeight + 'px');
				}
			});
		}
	};
}).filter('orderObjectBy', function() {
	return function(items, field, reverse) {
		var filtered = [];
		for (var key in items){
			var item = items[key];
			filtered.push(item);
		}
		filtered.sort(function (a, b) {
			return (a[field] > b[field] ? 1 : -1);
		});
		if(reverse) filtered.reverse();
		return filtered;
	};
}).directive('inviteMobile', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			if($(window).width() < 768){
				// we are
				// this means the navigation is hidden
				// we have to position the invite popover below the "add person" button
				element.addClass('invite-container-mobile');
			}
		}
	};
}).filter('filterUsersInConv', function() {
	return function(contacts) {
		var result = [];
		var users = Chat.scope.convs[Chat.scope.active.conv].users;
		angular.forEach(contacts, function(contact){
			if($.inArray(contact, users) === -1){
				result.push(contact);
			}
		});

		return result;
	};
});

