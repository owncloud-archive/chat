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
		'convs',
		'contacts',
		'backends',
		'title',
		'session',
		'time',
		function(
			$scope,
			$http,
			$filter,
			$interval,
			initvar,
			convs,
			contacts,
			backends,
			title,
			$session,
			Time
		){

			$(window).unload(function(){
				$scope.quit();
			});

			$scope.avatarsEnabled = $.trim($('#avatars-enabled').text());

			$CCS = $scope;

			$(document).ready(function(){
				$scope.emojis = $filter('toEmojiArray')(emojione.emojioneList);
			});

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
					"chat" : false,
					"initDone" : false,
					"settings" : false,
					"emojiContainer" : false,
					"invite" : false,
					"files" : false
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
				},
				/**
				 * TODO translations
				 */
				showFilePicker : function(){
					OCdialogs.filepicker('Please choose a file to attach to the conversation', function(paths){
						for(var key in paths){
							var path = paths[key];
							convs.attachFile($session.conv, path, Time.now(), $session.user);
						}
						var backend = $scope.convs[$session.conv].backend.id;
						backends[backend].handle.attachFile($session.conv, paths, $session.user);
					}, true);
				},
				showFiles : function(){
					$scope.view.toggle('files');

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
								// the clicked item contains the exception class
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
					$session.conv = convId;
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
					$session.conv = null;
				},
				/**
				 * This will focus the chat input field
				 */
				focusMsgInput : function(){
					$('#chat-msg-input-field');
				},
				unShare : function(convId, path, timestamp, user, key){
					var backend = $scope.convs[convId].backend.id;
					backends[backend].handle.removeFile(convId, path);
					convs.removeFile(convId, path, timestamp, user, key);
				},
                downloadFile : function(path){
                    var dir = '/';
                    var files = path;
                    OC.redirect(OC.generateUrl('/apps/files/ajax/download.php?dir={dir}&files={files}', {dir: dir, files:files}));
                },
				// Specific for XMPP
				addContactToRoster: function (convId) {
					backends.xmpp.handle.addContactToRoster(convId);
				},
				removeContactFromRoster : function (convId) {
					backends.xmpp.handle.removeContactFromRoster(convId);
				},
				saveContact : function (contactId) {
					var contact = contacts.contacts[contactId];
					contacts.addContacts([{
						"FN": contact.displayname,
						"IMPP": contact.id
					}], function (){
						delete contacts.contacts[contactId];
					});
				},
				startInstantChat: function () {
					backends.xmpp.handle.newConv($scope.fields.jid);
					$scope.fields.jid = '';
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
				if ($scope.fields.chatMsg !== '' && $scope.fields.chatMsg !== null){
					var backend = convs.get($session.conv).backend.id;
					convs.addChatMsg($session.conv, $session.user, $scope.fields.chatMsg, Time.now(), backend);
					backends[backend].handle.sendChatMsg($session.conv, $scope.fields.chatMsg);
					$scope.fields.chatMsg = '';
					setTimeout(function(){
						$('#chat-msg-input-field').trigger('autosize.resize');
					},1);
					$('#chat-msg-input-field').focus();

					for (var key in convs.get($session.conv).users) {
						var user =  convs.get($session.conv).users[key];
						if(user.id !== $session.user.id){
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

			$scope.contacts = contacts.contacts  // DON NOT USE THIS! ONLY FOR ATTACHING TO THE SCOPE
			$scope.backends = backends;

			/**
			 * @var {object} initConvs
			 */
			$scope.initConvs = {};

			$scope.isWindowActive = true;

			$scope.fields = {
				'chatMsg' : '',
			};

			$session.user = contacts.contacts[OC.currentUser]
			$session.id = initvar.sessionId;
			$scope.initConvs = initvar.initConvs;
			$scope.initvar = initvar;

			function init() {
				for (var key in backends){
					backends[key].handle.init();
				}


				$scope.initDone = true;
				//Now join and add all the existing convs
				for (var backendId in $scope.initConvs) {
					for (var key in $scope.initConvs[backendId]){
						var conv = $scope.initConvs[backendId][key];
						var contactsInConv = [];
						for (var key in conv.users) {
							var user = conv.users[key];
							contactsInConv.push(contacts.contacts[user]);
						}
						convs.addConv(conv.id, contactsInConv, backends[backendId], [], conv.files);
						for (var key in conv.messages) {
							var msg = conv.messages[key];
							convs.addChatMsg(conv.id, contacts.contacts[msg.user], msg.msg, msg.timestamp, backends[backendId], true);
						}
						for (var key in conv.files){
							var file = conv.files[key];
							convs.addChatMsg(conv.id, file.user, tran('translations-attached', {displayname: file.user.displayname, path: file.path}),
								file.timestamp, backendId);
						}
					}
				}
				$scope.makeFirstConvActive();
				$scope.$session = $session;
			}

			/**
			 * Function called when the app is quit
			 */
			$scope.quit = function(){
				for(var id in backends){
					backends[id].handle.quit();
				}
			};

			/**
			 * This function will make the first conversation in the conversation list active
			 */
			$scope.makeFirstConvActive = function(){
				firstConv = convs.getFirstConv();
				if(firstConv === undefined){
					$session.conv = null;
					$scope.view.hide('chat');
					$scope.view.show('emptyMsg');
				} else {
					$scope.view.makeActive(firstConv);
				}
			};

			/**
			 * This function will invite the userToInvite for the $scope.$session.conv
			 * This will make the order of the conv the highest
			 * @param {object} userToInvite
			 */
			$scope.invite = function(userToInvite){
				var backend = $scope.convs[$session.conv].backend.id;
				var groupConv = $scope.convs[$session.conv].users.length > 2;
				backends[backend].handle.invite($session.conv, userToInvite, groupConv, function(response){
					if(groupConv) {
						$scope.view.replaceUsers($session.conv, response.data.users);
					} else {
						convs.addConv(response.data.conv_id, response.data.users, $scope.convs[$session.conv].backend, response.data.messages, []);
					}
				});
				$scope.view.hide('invite');
				$scope.view.makeActive($session.conv);

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
					if($scope.isWindowActive === false) {
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
				$scope.isWindowActive= true;
			};

			window.onblur = function () {
				$scope.isWindowActive = false;
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

			$scope.contactInRoster = function (id) {
				return backends.xmpp.handle.contactInRoster(id);
			};

			$scope.contactInContacts = function (convId) {
				var contact = contacts.findByBackendValue('xmpp', convId);
				return contact.saved;
			};

			init();
		}
	]
);

