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
//		$scope.$apply();
	for (var active in $scope.backends) break;
	$scope.active.backend =  $scope.backends[active];
	angular.forEach($scope.backends, function(backend, namespace){
		if(namespace === 'och'){
			Chat[namespace].util.init();
		}
	});
	$scope.initDone = true;


	$scope.quit = function(){
		angular.forEach($scope.backends, function(backend, namespace){
			if(namespace === 'och'){
				Chat[namespace].util.quit();
			}
		});
	};

	$scope.selectBackend = function(backend){
		$scope.active.backend = backend;
	};

	$scope.view = {
		elements : {
			"newConv" : true,
			"chat" : false,
			"initDone" : false,
			"settings" : false,
			"emojiContainer" : false,
			"invite" : false,
			'archived' : false,
			'showArchived' : {'bold' : false}
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
		hide : function(element, $event, exception){
			if($event !== undefined){
				var classList = $event.target.classList;
				if(classList.contains(exception)){
					// the clicked item containted the exception class
					// this mean probably that we clicked on the item in side the viewed div
					// thus the div don't need to be hided;
					return;
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
			$scope.view.hide('newConv');
			$scope.view.show('chat', $event, exception);
			$scope.active.conv = convId;
			$scope.view.focusMsgInput();
			$scope.convs[convId].new_msg = false;
			if($scope.convs[convId].archived && $scope.view.elements.archived === false ){
				$scope.view.show('archived');
			}

		},
		unActive : function(){
			$scope.active.conv = null;
		},
		addConv : function(convId, users, backend, msgs, archived){
			if(archived === undefined){
				archived = false;
			}
			if($scope.convs[convId] === undefined) {
				$scope.convs[convId] = {
					id : convId,
					users : users,
					msgs : [],
					backend : backend,
					archived : archived,
					new_msg : false,
					raw_msgs : []
				};
				if(!archived){
					$scope.view.makeActive(convId);
				}
				if(msgs !== undefined){
					angular.forEach(msgs, function(msg){
						$scope.view.addChatMsg(convId, Chat.scope.contactsObj[msg.user], msg.msg, msg.timestamp, backend);
					});
				}
//				$scope.$apply();
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

			setTimeout(function(){
				Chat.app.ui.scrollDown();
			},1); // Give angular some time to apply the msg to scope
			// Edit tab title when the tab isn't active
			if(user !== $scope.active.user) {
				Chat.tabTitle = 'New msg from ' + user;
			}

			// Add raw msgs to raw_msgs
			$scope.convs[convId].raw_msgs.push({"msg" : msg, "timestamp" : timestamp, "user" : user});
		},
		addUserToConv : function(convId, user){
			if($scope.convs[convId].users.indexOf(user) === -1){
				$scope.convs[convId].users.push(user);
			}
		},
		focusMsgInput : function(){
			Chat.app.ui.focusMsgInput();
		},
		replaceUsers : function(convId, users){
			$scope.convs[convId].users = users;
		},
		notifyMsgInConv : function(convId){
			$scope.convs[convId].new_msg = true;
		}
	};


	$scope.sendChatMsg = function(){
		if ($scope.fields.chatMsg !== ''){
			var backend = $scope.convs[$scope.active.conv].backend.name;
			$scope.view.addChatMsg($scope.active.conv, $scope.active.user, $scope.fields.chatMsg, new Date().getTime() / 1000, backend);
			Chat[backend].on.sendChatMsg($scope.active.conv, $scope.fields.chatMsg);
			$scope.debug.push($scope.fields.chatMsg);
			$scope.fields.chatMsg = '';
			setTimeout(function(){
				$('#chat-msg-input-field').trigger('autosize.resize');
			},1);
		}
	};

	$scope.startNewConv = function(userToInvite){
		var backend = $scope.active.backend;
		Chat[backend.name].on.newConv([userToInvite], function(convId, users, msgs){
			$scope.view.addConv(convId, users, backend, msgs);
		});
	};

	$scope.toggleArchive = function(convId){
		var backend = $scope.convs[convId].backend.name;
		$scope.convs[convId].archived = !$scope.convs[convId].archived;
		if($scope.convs[convId].archived === true){
			Chat[backend].on.archive(convId);
		} else if ($scope.convs[convId].archived === false){
			Chat[backend].on.unArchive(convId);
		}

		firstConv = Chat.scope.getFristConv();
		if(firstConv === undefined){
			$scope.view.hide('chat');
			$scope.view.show('newConv');
		} else {
			$scope.view.makeActive(firstConv);
		}
	};

	$scope.getFristConv = function(){
		for (firstConv in $scope.convs) break;
		if($scope.convs[firstConv].archived === true){
			return undefined;
		} else {
			return firstConv;
		}
	}

	$scope.invite = function(userToInvite){
		var backend = $scope.convs[$scope.active.conv].backend.name;
		if(userToInvite === $scope.active.user){
			Chat.app.ui.alert('You can\'t invite yourself');
		} else if(userToInvite === ''){
			Chat.app.ui.alert('Please provide a user name');
		} else {
			Chat[backend].on.invite($scope.active.conv, userToInvite);
		}
		$scope.view.hide('invite');
		$scope.view.makeActive($scope.active.conv);
	};

	$scope.findContactByUser = function(user, namespace){
		var backend = $scope.backends[namespace];
		var result;
		var contacts = $filter('backendFilter')($scope.contacts, backend);
		contacts.forEach(function(contact, index){
			if(contact.backends[namespace].value === user){
				result = contact;
			}
		});
		return result;
	};

	function updateContacts(){
		var url = OC.generateUrl('/apps/chat/contacts')
		$http.get('/index.php/apps/chat/contacts?requesttoken=' + oc_requesttoken)
			.success(function(data, status) {
				$scope.contacts = data.contacts;
				$scope.contactsObj = data.contactsObj;
			});
	};

	$interval(updateContacts, 10000);

	setInterval(function(){
//		$scope.$apply();
		if($scope.title.title === ''){
			$('title').text($scope.title.default);
		} else {
			$('title').text($scope.title.title);
		}
	}, 1000);
	
	setInterval(function(){
		$('title').text($scope.title.default);
	}, 2000);

	$scope.$watchCollection('title.new_msgs', function(){
		if($scope.active.window === false){
			var title = 'New messages from ';
			if($scope.title.new_msgs.length === 0 ){
				title = '';
			} else {
				angular.forEach($scope.title.new_msgs, function(user){
					title = title + user + " ";
				});
			}
			$scope.title.title = title;
		} else {
			$scope.title.tile = '';
		}
//		$scope.$apply();
	});

	$scope.notify = function(user){
		if($scope.title.new_msgs.indexOf(user) == -1){
			$scope.title.new_msgs.push(user);
		}
//		$scope.$apply();
	};

	window.onfocus = function () {
		$scope.title.title = '';
		$scope.title.new_msgs = [];
		$scope.active.window = true;
//		$scope.$apply();
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
		$scope.fields.chatMsg = textBefore + name + textAfter;
	};

	$scope.$watch('convs', function(){
		var bold  = false;
		var forLoop = true;
		for(index in $scope.convs){
			conv = $scope.convs[index];
			if(forLoop === true){
				if(conv.archived === true && conv.new_msg === true) {
					bold = true;
					forLoop = false;
				}
			}
		}
		if(bold){
			$scope.view.elements.showArchived.bold = true;
		} else {
			$scope.view.elements.showArchived.bold = false;
		}
	}, true);

	$scope.emojis = Chat.app.util.emojis;

}]).directive('avatar', function() {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			element.applyContactAvatar(attrs.addressbookBackend, attrs.addressbookId, attrs.id, attrs.displayname, attrs.size);
			if(attrs.onlinesize !== undefined){
				element.online(attrs.isonline, attrs.onlinesize);
				$scope.$watch('contactsObj', function(){
					element.online(Chat.scope.contactsObj[attrs.id].online, attrs.onlinesize);
				});
			}
		}
	};
}).filter('backendFilter', function() {
	return function(contacts, backend) {
		if(contacts === null || backend === null){
			// Not inited yet
			return;
		}
		// backend = the active backend
		var output = [];
		contacts.forEach(function(contact, index){
			angular.forEach(contact.backends, function(contactBackend, index){
				if(contactBackend.protocol === backend.protocol){
					output.push(contact);
				}
			});
		});
		return output;
	};
}).filter('userFilter', function() {
	return function(users) {
		var output = [];
		users.forEach(function(user, index){
			if(user.id !== Chat.scope.active.user.id){
				output.push(user);
			}
		});
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
}).directive('moreUsers', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			var msg = '';
			users = JSON.parse(attrs.users);
			angular.forEach(users, function(user, key){
				if(key > 3){
					if(key === users.length-1){
						msg += user.displayname;
					} else {
						msg += user.displayname + ', ';
					}
				}
			});
			element.attr('title', msg);
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
			angular.forEach(users, function(user, key){
				if(user.id !== Chat.scope.active.user.id){
					text += user.displayname + ' ';
				}
			});
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
});

