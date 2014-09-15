/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
angular.module('chat').controller('ConvController', ['$scope', '$http', '$filter', '$interval', 'initvar', function($scope, $http, $filter, $interval, initvar) {
	/**
	 * @var {object} convs
	 */
	$scope.convs = {};

	/**
	 * Contacts stored as an array with contact objects
	 * @var {array} contacts
	 */
	$scope.contacts = [];

	/**
	 * Contacts storead as an array with id's
	 * @var {array} convs
	 */
	$scope.contactsList = [];

	/**
	 * @var {object} backends
	 */
	$scope.backends = [];

	/**
	 * @var {object} initConvs
	 */
	$scope.initConvs = {};

	/**
	 * @var {object} active
	 */
	$scope.active = {
		/**
		 * Stores the active backend
		 * @var {object} backend
		 */
		backend : {},
		/**
		 * Stores the active conv id
		 * @var {string} conv
		 */
		conv : {},
		/**
		 * @var {bool} window
		 */
		window : true,
	};
	/**
	 * @var {object} title
	 */
	$scope.title = {};
	/**
	 * @var {object} title
	 */
	$scope.title.title = "";
	/**
	 * @var {object} default
	 */
	$scope.title.default = "Chat - ownCloud";
	/**
	 * @var {array} new_msgs
	 */
	$scope.title.new_msgs = [];
	/**
	 * @var {object} fields
	 */
	$scope.fields = {
		'chatMsg' : '',
	};
	
	Chat.scope = angular.element($("#app")).scope();
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

	/**
	 * Function called when the app is quit
	 */
	$scope.quit = function(){
		for(var namespace in $scope.backends){
			var backend = $scope.backends[namespace];
			if(namespace === 'och'){
				Chat[namespace].util.quit();
			}
		}
	};

	/**
	 * Object used to interact with the view
	 * @var {object} view
	 */
	$scope.view = {
		/**
		 * Stores whether an element should be visible
		 * @var {object} elements
		 */
		elements : {
			"emptyMsg" : true,
			"chat" : false,
			"initDone" : false,
			"settings" : false,
			"emojiContainer" : false,
			"invite" : false,
		},
		/**
		 * Called when an invite button is clicked
		 */
		inviteClick : function(){
			$scope.view.toggle('invite');
			// Focus search field
			setTimeout(function(){
				$('#invite-search-field').focus();
			}, 1);
		},
		/**
		 * Called when the popover button is clicked
		 */
		showEmojiPopover : function(){
			var height = $("#chat-window-footer").height();
			$scope.view.toggle('emojiContainer');
			setTimeout(function(){
				$('#emoji-container').css('bottom', height + 20);
			},1);
			console.log(height);
		},
		/**
		 * This will flag the element as visible in the view.elements array
		 * @param {string} element the element which should be made visible (should be in the $scope.view.elements array )
		 * @param {object} $event
		 * @param {string} exception this function call will be ignored when this element is clicked
		 */
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
		/**
		 * This will flag the element as invisible in the view.elements array
		 * @param {string} element the element which should be made invisible (should be in the $scope.view.elements array )
		 * @param {object} $event
		 * @param {string} exceptions this function call will be ignored when this element is clicked
		 */
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
		/**
		 * This will toggle the visibility of the element in the view.elements array
		 * @param {string} element the element which should be made invisible (should be in the $scope.view.elements array )
		 */
		toggle : function(element){
			$scope.view.elements[element] = !$scope.view.elements[element];
		},
		/**
		 * This function will update the title of the page
		 * @param {string} newTitle
		 */
		updateTitle : function(newTitle){
			$scope.title = newTitle;
		},
		/**
		 * This function will make an conv active
		 * @param {string} convId
		 * @param {object} $event
		 * @param {string} exception - when this is provided the function call will be ignored when it was this param which was clicked
		 */
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
		/**
		 * This will unActive all conversations
		 */
		unActive : function(){
			$scope.active.conv = null;
		},
		/**
		 * This will add an conv to the view
		 * @param {string} convId
		 * @param {array} users
		 * @param {object} backend
		 * @param {array} msgs
		 */
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
		/**
		 * This will add an chat msg to an existing conversation
		 * This will call the notify() function when the message isn't send by the active user
		 * This will call the notifyMsgInConv() function when it isn't the active conv
		 * This will make the order of the conv the highest
		 * @param {string} convId
		 * @param {object} user
		 * @param {string} msg
		 * @param {int} timestamp
		 * @param {object} backend
		 * @param {bool} noNotify
		 */
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
		/**
		 * This will add an user to an conversation
		 * @param {string} convId
		 * @param {object} user
		 */
		addUserToConv : function(convId, user){
			if($scope.convs[convId].users.indexOf(user) === -1){
				$scope.convs[convId].users.push(user);
			}
		},
		/**
		 * This will focus the chat input field
		 */
		focusMsgInput : function(){
			$('#chat-msg-input-field');
		},
		/**
		 * This will replace the users in an existing conversation
		 * @param {string} convId
		 * @param {array} users - array with objects
		 */
		replaceUsers : function(convId, users){
			$scope.convs[convId].users = users;
		},
		/**
		 * This will bold the conversation name in the view
		 * @param {string} convId
		 */
		notifyMsgInConv : function(convId){
			$scope.convs[convId].new_msg = true;
		},
		/**
		 * This will flag a contact in the $scope.contactsObj as online
		 * @param {string} userId
		 */
		makeUserOnline : function(userId){
			$scope.contactsObj[userId].online = true;
		},
		/**
		 * This will flag a contact in the $scope.contactsObj as offline
		 * @param {string} userId
		 */
		makeUserOffline : function(userId){
			$scope.contactsObj[userId].online = false;
		}
	};

	/**
	 * This function is called when the user tries to send a chat msg
	 * This will call the sendChatMsg function on the correct backend
	 * This will add the chatMsg to the conv
	 * This will empty the chat field
	 * This will make the order of the contacts in the conv the highest
	 */
	$scope.sendChatMsg = function(){
		if ($scope.fields.chatMsg !== ''){
			var backend = $scope.convs[$scope.active.conv].backend.name;
			$scope.view.addChatMsg($scope.active.conv, $scope.active.user, $scope.fields.chatMsg, Time.now(), backend);
			Chat[backend].on.sendChatMsg($scope.active.conv, $scope.fields.chatMsg);
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

	/**
	 * This function will make the first conversation in the conversation list active
	 */
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

	/**
	 * This function will return the first conversation in the conversation list
	 * @returns {object|undefined}
	 */
	$scope.getFirstConv = function(){
		for (firstConv in $scope.convs) break;
		if (typeof firstConv !== 'undefined') {
			return firstConv;
		} else {
			return undefined;
		}
	}

	/**
	 * This function will invite the userToInvite for the $scope.active.conv
	 * This will make the order of the conv the highest
	 * @param {object} userToInvite
	 */
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

	/**
	 * This interval will make the title of the page $scope.title.title every second
	 */
	$interval(function(){
		if($scope.title.title === ''){
			$('title').text($scope.title.default);
		} else {
			$('title').text($scope.title.title);
		}
	}, 1000);
	/**
	 * This interval will make the title of the page $scope.title.default every two second
	 */
	$interval(function(){
		$('title').text($scope.title.default);
	}, 2000);

	/**
	 * This will check if the window is active
	 *  if so
	 *   it will change the title of the page into 'New messages from {user}'
	 *  otherwise
	 *   it will empty the title
	 */
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

	/**
	 * This function will add the user to the $scope.title.new_msgs array
	 * This way the user can be notified about a new msgs
	 * @param {string} user
	 */
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

	/**
	 * This function will add the emoji to the current position of the cursor in the chat msg input field
	 * it will place a space before and after the emoji
	 * @param name
	 */
	$scope.addEmoji = function(name){
		var element = $("#chat-msg-input-field");
		element.focus(); //ie
		var selection = element.getSelection();
		var textBefore = $scope.fields.chatMsg.substr(0, selection.start);
		var textAfter = $scope.fields.chatMsg.substr(selection.end);
		$scope.fields.chatMsg = textBefore + ' ' + name + ' ' + textAfter + ' ';
		$scope.view.hide('emojiContainer');
	};

	$scope.emojis = Chat.app.util.emojis;

	/**
	 * When there are new messages in the active conversation the chat window must be scroll to the bottom
	 */
	$scope.$watch('convs[active.conv].msgs', function(){
		setTimeout(function(){
			$('#chat-window-msgs').scrollTop($('#chat-window-msgs')[0].scrollHeight);
		},250);
	}, true);

	/**
	 * This function returns the order of an conversation increased with 1
	 * @returns {int}
	 */
	$scope.getHighestOrder = function(){
		var sortedConvs = $filter('orderObjectBy')($scope.convs, 'order');
		if(sortedConvs[sortedConvs.length - 1] !== undefined){
			return sortedConvs[sortedConvs.length - 1].order + 1;
		} else {
			return 1;
		}
	};

	/**
	 * This function returns the order of an contact increased with 1
	 * @returns {int}
	 */
	$scope.getHighestOrderContacts = function(){
		var sortedContacts = $filter('orderObjectBy')($scope.contactsObj, 'order');
		return sortedContacts[sortedContacts.length - 1].order + 1;
	};

}]);

