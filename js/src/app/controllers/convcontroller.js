/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
angular.module('chat').controller(
	'ConvController',
	[
		'$scope',
		'$http',
		'$filter',
		'$interval',
		'initvar',
		'och',
		'convs',
		'activeUser',
		'contacts',
		'backends',
		'title',
		function(
			$scope,
			$http,
			$filter,
			$interval,
			initvar,
			och,
			convs,
			activeUser,
			contacts,
			backends,
			title
		){


			Chat.scope = $scope;

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
					convs.get(convId).new_msg = false;
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
				 * This will focus the chat input field
				 */
				focusMsgInput : function(){
					$('#chat-msg-input-field');
				},
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
					convs.addChatMsg($scope.active.conv, $scope.active.user, $scope.fields.chatMsg, Time.now(), backend);
					backends[backend].handle.sendChatMsg($scope.active.conv, $scope.fields.chatMsg);
					$scope.fields.chatMsg = '';
					var order = contacts.getHighestOrder();
					setTimeout(function(){
						$('#chat-msg-input-field').trigger('autosize.resize');
					},1);
					$('#chat-msg-input-field').focus();

					for (var key in $scope.convs[$scope.active.conv].users) {
						var user =  $scope.convs[$scope.active.conv].users[key];
						if(user.id !== $scope.active.user.id){
							var order = contacts.getHighestOrder();
							contacts.contacts[user.id].order = order;
						}
					}
				}
			};

			/**
			 * @var {object} convs
			 */
			$scope.convs =  convs.convs; // DON NOT USE THIS! ONLY FOR ATTACHING TO THE SCOPE

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
			$scope.fields = {
				'chatMsg' : '',
			};

			$scope.active.user = activeUser;
			$scope.initConvs = initvar.initConvs;
			$scope.initvar = initvar;
			for (var active in backends) break;
			$scope.active.backend =  backends[active];

			function init() {
				for (var key in backends){
					backends[key].handle.init();
				}


				$scope.initDone = true;
				//Now join and add all the existing convs
				for (var key in $scope.initConvs.och) {
					var conv = $scope.initConvs.och[key];
					var contactsInConv = [];
					for (var key in conv.users) {
						var user = conv.users[key];
						contactsInConv.push(contacts.contacts[user]);
					}
					convs.addConv(conv.id, contactsInConv, backends.och, []);
					for (var key in conv.messages) {
						var msg = conv.messages[key];
						convs.addChatMsg(conv.id, contacts.contacts[msg.user], msg.msg, msg.timestamp, backends.och, true);
					}
				}
			}

			/**
			 * Function called when the app is quit
			 */
			$scope.quit = function(){
				for(var namespace in backends){
					var backend = backends[namespace];
					if(namespace === 'och'){
						backends[namespace].handle.quit();
					}
				}
			};

			/**
			 * This function will make the first conversation in the conversation list active
			 */
			$scope.makeFirstConvActive = function(){
				firstConv = convs.getFirstConv();
				if(firstConv === undefined){
					$scope.active.conv = null;
					$scope.view.hide('chat');
					$scope.view.show('emptyMsg');
				} else {
					$scope.view.makeActive(firstConv);
				}
			};

			/**
			 * This function will invite the userToInvite for the $scope.active.conv
			 * This will make the order of the conv the highest
			 * @param {object} userToInvite
			 */
			$scope.invite = function(userToInvite){
				var backend = $scope.convs[$scope.active.conv].backend.name;
				var groupConv = $scope.convs[$scope.active.conv].users.length > 2;
				backends[backend].handle.invite($scope.active.conv, userToInvite, groupConv, function(response){
					if(groupConv) {
						$scope.view.replaceUsers($scope.active.conv, response.data.users);
					} else {
						convs.addConv(response.data.conv_id, response.data.users, $scope.convs[$scope.active.conv].backend, response.data.messages);
					}
				});
				$scope.view.hide('invite');
				$scope.view.makeActive($scope.active.conv);

				var order = contacts.getHighestOrder();
				contacts.contacts[userToInvite.id].order = order;

			};

			/**
			 * This interval will make the title of the page $scope.title.title every second
			 */
			$interval(function(){
				if (title.getTitle() === '') {
					$('title').text(title.getDefaultTitle());
				} else {
					if($scope.active.window === false) {
						$('title').text(title.getTitle());
					}
				}
			}, 1000);
			/**
			 * This interval will make the title of the page $scope.title.default every two second
			 */
			$interval(function(){
				$('title').text(title.getDefaultTitle());
			}, 2000);


			window.onfocus = function () {
				title.updateTitle('');
				title.emptyNewMsgs();
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

			init();
		}
	]
);

