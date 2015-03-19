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


			//$scope.msgs = $filter('orderBy')([{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"test","timestamp":1424439328,"time":{"hours":14,"minutes":"35","seconds":28},"time_read":"February 20, 2015 2:35","avatar":true,"$$hashKey":"object:178"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"test","timestamp":1424439327,"time":{"hours":14,"minutes":"35","seconds":27},"time_read":"February 20, 2015 2:35","avatar":false,"$$hashKey":"object:177"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"test","timestamp":1424439325,"time":{"hours":14,"minutes":"35","seconds":25},"time_read":"February 20, 2015 2:35","avatar":false,"$$hashKey":"object:176"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":" Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque faucibus nulla ligula, sit amet semper leo pellentesque ut. Maecenas ac nulla luctus, auctor metus eu, dignissim ipsum. Sed gravida ipsum diam, et fermentum magna sodales in. Sed aliquam, mauris ac eleifend pulvinar, turpis est luctus eros, ut tristique nisl nisi ut velit. Praesent ornare libero at nibh pharetra, quis tristique ex tristique. Cras elementum imperdiet accumsan. Morbi lacinia a nisl eu volutpat. Aenean elementum libero vestibulum nulla sagittis hendrerit. In vulputate tortor quis ornare gravida. ","timestamp":1424438072,"time":{"hours":14,"minutes":"14","seconds":32},"time_read":"February 20, 2015 2:14","avatar":false,"$$hashKey":"object:175"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":" Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque faucibus nulla ligula, sit amet semper leo pellentesque ut. Maecenas ac nulla luctus, auctor metus eu, dignissim ipsum. Sed gravida ipsum diam, et fermentum magna sodales in. Sed aliquam, m","timestamp":1424438014,"time":{"hours":14,"minutes":"13","seconds":34},"time_read":"February 20, 2015 2:13","avatar":false,"$$hashKey":"object:174"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":" Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque faucibus nulla ligula, sit amet semper leo pellentesque ut. Maecenas ac nulla luctus, auctor metus eu, dignissim ipsum. Sed gravida ipsum diam, et fermentum magna sodales in. Sed aliquam, m","timestamp":1424437955,"time":{"hours":14,"minutes":"12","seconds":35},"time_read":"February 20, 2015 2:12","avatar":false,"$$hashKey":"object:173"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":" Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque faucibus nulla ligula, sit amet semper leo pellentesque ut. Maecenas ac nulla luctus, auctor metus eu, dignissim ipsum. Sed gravida ipsum diam, et fermentum magna sodales in. Sed aliquam, m","timestamp":1424437953,"time":{"hours":14,"minutes":"12","seconds":33},"time_read":"February 20, 2015 2:12","avatar":false,"$$hashKey":"object:172"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":" Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque faucibus nulla ligula, sit amet semper leo pellentesque ut. Maecenas ac nulla luctus, auctor metus eu, dignissim ipsum. Sed gravida ipsum diam, et fermentum magna sodales in. Sed aliquam, m","timestamp":1424437950,"time":{"hours":14,"minutes":"12","seconds":30},"time_read":"February 20, 2015 2:12","avatar":false,"$$hashKey":"object:171"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"test","timestamp":1424437922,"time":{"hours":14,"minutes":"12","seconds":2},"time_read":"February 20, 2015 2:12","avatar":false,"$$hashKey":"object:170"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test9","timestamp":1423994096,"time":{"hours":10,"minutes":"54","seconds":56},"time_read":"February 15, 2015 10:54","avatar":true,"$$hashKey":"object:168"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test10","timestamp":1423994096,"time":{"hours":10,"minutes":"54","seconds":56},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:169"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test8","timestamp":1423994095,"time":{"hours":10,"minutes":"54","seconds":55},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:167"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test7","timestamp":1423994094,"time":{"hours":10,"minutes":"54","seconds":54},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:166"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test6","timestamp":1423994093,"time":{"hours":10,"minutes":"54","seconds":53},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:165"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test5","timestamp":1423994092,"time":{"hours":10,"minutes":"54","seconds":52},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:164"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test4","timestamp":1423994091,"time":{"hours":10,"minutes":"54","seconds":51},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:163"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test","timestamp":1423994090,"time":{"hours":10,"minutes":"54","seconds":50},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:162"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test","timestamp":1423994088,"time":{"hours":10,"minutes":"54","seconds":48},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:161"},{"contact":{"id":"derp","online":false,"displayname":"derp","order":7,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"derp"}],"address_book_id":"","address_book_backend":"local","$$hashKey":"object:77"},"msg":"test1","timestamp":1423994087,"time":{"hours":10,"minutes":"54","seconds":47},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:160"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"59","timestamp":1423994054,"time":{"hours":10,"minutes":"54","seconds":14},"time_read":"February 15, 2015 10:54","avatar":true,"$$hashKey":"object:158"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"60","timestamp":1423994054,"time":{"hours":10,"minutes":"54","seconds":14},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:159"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"58","timestamp":1423994053,"time":{"hours":10,"minutes":"54","seconds":13},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:157"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"56","timestamp":1423994052,"time":{"hours":10,"minutes":"54","seconds":12},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:155"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"57","timestamp":1423994052,"time":{"hours":10,"minutes":"54","seconds":12},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:156"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"55","timestamp":1423994051,"time":{"hours":10,"minutes":"54","seconds":11},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:154"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"54","timestamp":1423994050,"time":{"hours":10,"minutes":"54","seconds":10},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:153"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"5","timestamp":1423994049,"time":{"hours":10,"minutes":"54","seconds":9},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:152"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"52","timestamp":1423994048,"time":{"hours":10,"minutes":"54","seconds":8},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:151"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"51","timestamp":1423994047,"time":{"hours":10,"minutes":"54","seconds":7},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:150"},{"contact":{"id":"admin","online":true,"displayname":"admin","order":1,"saved":true,"backends":[{"id":"email","displayname":"E-mail","protocol":"email","namespace":" email","value":[[]]},{"id":"och","displayname":"ownCloud Chat","protocol":"x-owncloud-handle","namespace":"och","value":"admin"}],"address_book_id":"","address_book_backend":"local"},"msg":"50","timestamp":1423994046,"time":{"hours":10,"minutes":"54","seconds":6},"time_read":"February 15, 2015 10:54","avatar":false,"$$hashKey":"object:149"}]
			//	, 'timestamp');

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
					"chat" : true,
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
					//$scope.view.hide('emptyMsg');
					//$scope.view.show('chat', $event, exception);
					$session.conv = convId;
					$scope.view.focusMsgInput();
					convs.get(convId).new_msg = false;
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
					$('#chat-msg-input-field').focus();
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
			$scope.sendChatMsg = function(msg){
				if (msg !== '' && msg !== null){
					var backend = convs.get($session.conv).backend.id;
					convs.addChatMsg($session.conv, $session.user, msg, Time.now(), backend);
					backends[backend].handle.sendChatMsg($session.conv, msg);
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
			$scope.convs =  convs.convs; // DO NOT USE THIS! ONLY FOR ATTACHING TO THE SCOPE SO IT CAN BE USED IN THE VIEW
			$scope.contacts = contacts.contacts // DO NOT USE THIS! ONLY FOR ATTACHING TO THE SCOPE SO IT CAN BE USED IN THE VIEW
			$scope.backends = backends;
			$scope.msgs = [];

			$scope.$watch('convs', function () {
				$scope.msgs = $filter('orderBy')(convs.convs[$session.conv].msgs, 'timestamp');
			},true);

			$scope.$watch('$session.conv', function () {
				$scope.msgs = $filter('orderBy')(convs.convs[$session.conv].msgs, 'timestamp');
				$scope.$broadcast('scrollBottom');
			});

			window.onresize = function(event) {
				$scope.$broadcast('scrollBottom');
			};
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

			$scope.loadOldMessages = function (convId) {
				// first count the current messages
				var amount = convs.get(convId).msgs.length;
				// next calculate the position of the next 10 messages
				var backend = convs.get(convId).backend.id;
				backends[backend].handle.getOldMessages(convId, amount, 10, function (msgs) {
					console.log(msgs);
					for (var key in msgs){
						var msg = msgs[key];
						convs.addChatMsg(convId, contacts.contacts[msg.user], msg.msg, msg.timestamp, backend);
					}
				});
			};

			$scope.toTrust = function(html_code) {
				return $sce.trustAsHtml(html_code);
			}
			init();
		}
	]
);

