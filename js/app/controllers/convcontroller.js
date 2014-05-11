Chat.angular.controller('ConvController', ['$scope', '$filter', function($scope, $filter) {
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
		contactsToStartConvWith : {
		},
	};
	
	$scope.init = function(){
		var initvar = JSON.parse($('#initvar').text());
		$scope.contacts = initvar['contacts'];
		$scope.contactsList = initvar['contactsList'];
		$scope.contactsObj = initvar['contactsObj'];
		$scope.backends = initvar['backends'];
		$scope.active.user = $scope.contactsObj[OC.currentUser];
		$scope.initConvs = initvar['initConvs'];
		$scope.initvar = initvar;
		$scope.$apply();
		for (active in $scope.backends) break;
		$scope.active.backend =  $scope.backends[active];
		angular.forEach($scope.backends, function(backend, namespace){
			if(namespace === 'och'){
				Chat[namespace].util.init();
			}
		});
		$scope.initDone = true;
		setInterval($scope.updateContacts, 10000);
	};

	$scope.quit = function(){
		angular.forEach($scope.backends, function(backend, namespace){
			if(namespace === 'och'){
				Chat[namespace].util.quit();
			}
		});
	}

	$scope.selectBackend = function(backend){
		$scope.active.backend = backend;
	};

	$scope.view = {
		elements : {
			"contact" : true,
			"chat" : false,
			"initDone" : false,
			"settings" : false,
			"emojiContainer" : false
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
			$scope.view.hide('contact');
			$scope.view.show('chat', $event, exception);
			$scope.active.conv = convId;
			$scope.view.focusMsgInput();
		},
		unActive : function(){
			$scope.active.conv = null;
		},
		addConv : function(convId, users, backend, msgs){
			//users.push($scope.active.user);
			$scope.convs[convId] = {
				id : convId,
				users : users,
				msgs : [],
				backend : backend,
			};
			$scope.view.makeActive(convId);
			if(msgs !== undefined){
				angular.forEach(msgs, function(msg){
					$scope.view.addChatMsg(convId, Chat.scope.contactsObj[msg.user], msg.msg, msg.timestamp, backend);
				});
			}
			$scope.$apply();
			
		},
		addChatMsg : function(convId, user, msg, timestamp, backend){
			if(user.id !== $scope.active.user.id){
				$scope.notify(user.displayname);
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
		},
		addUserToConv : function(convId, user){
			if($scope.convs[convId].users.indexOf(user) === -1){
				$scope.convs[convId].users.push(user);
			}
		},
		focusMsgInput : function(){
			Chat.app.ui.focusMsgInput();
		},
	};


	$scope.sendChatMsg = function(){
		if ($scope.fields.chatMsg != ''){
			var backend = $scope.convs[$scope.active.conv].backend.name;
			$scope.view.addChatMsg($scope.active.conv, $scope.active.user, $scope.fields.chatMsg, new Date().getTime() / 1000, backend);
			Chat[backend].on.sendChatMsg($scope.active.conv, $scope.fields.chatMsg);
			$scope.debug.push($scope.fields.chatMsg);
			$scope.fields.chatMsg = '';
			setTimeout(function(){
				$('#chat-msg-input-field').trigger('autosize.resize');
			},1)
		}
	};

	$scope.newConv = function(){
		var backend = $scope.active.backend;
		var usersToInvite = $scope.fields.contactsToStartConvWith;
		Chat[backend.name].on.newConv(usersToInvite, function(convId, users, msgs){
			$scope.view.addConv(convId, users, backend, msgs);
		});
		$scope.fields.contactsToStartConvWith = {};
	};

	$scope.leave = function(convId){
		var backend = $scope.convs[convId].backend.name;
		Chat[backend].on.leave(convId, function(){
			delete $scope.convs[convId];
			if(Chat.app.util.countObjects($scope.convs) === 0){
				$scope.view.hide('chat');
				$scope.view.show('contact');
			} else {
				$scope.view.makeActive(Chat.app.ui.getFirstConv());
			}
		});
	};

	$scope.invite = function(userToInvite){
		/*var backend = $scope.convs[$scope.active.conv].backend.name;
		if(userToInvite === $scope.active.user){
			Chat.app.ui.alert('You can\'t invite yourself');
		} else if(userToInvite === ''){
			Chat.app.ui.alert('Please provide a user name');
		} else {
			Chat[backend].on.invite($scope.active.conv, userToInvite);
		}
		$scope.view.hide('inviteInput');
	*/
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

	$scope.updateContacts = function(){
		$.post('/index.php' + OC.linkTo("chat", "contacts")).done(function(response){
			$scope.contacts = response.contacts;
			$scope.contactsObj = response.contactsObj;
			$scope.$apply();
		});
	};

	
    setInterval(function(){
    	$scope.$apply();
    	if($scope.title.title == ''){
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
	    	if($scope.title.new_msgs.length == 0 ){
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
    	$scope.$apply();
	});
    
    $scope.notify = function(user){
    	if($scope.title.new_msgs.indexOf(user) == -1){
    	  	$scope.title.new_msgs.push(user);
    	}
    	$scope.$apply();
    };
    
    window.onfocus = function () { 
		$scope.title.title = '';
		$scope.title.new_msgs = [];
    	$scope.active.window = true; 
    	$scope.$apply();
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
  	}
  	
  	
  	$scope.emojis = Chat.app.util.emojis;
  	
  	$scope.updateContactsToStartConvWith = function(contact){
  		if($scope.fields.contactsToStartConvWith[contact.id]){
  			delete $scope.fields.contactsToStartConvWith[contact.id];
  		} else {
  			$scope.fields.contactsToStartConvWith[contact.id] = contact;
  		}
  	};
  	
}]).directive('avatar', function() {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			element.applyContactAvatar(attrs.addressbookBackend, attrs.addressbookId, attrs.id, attrs.displayname, attrs.size);
			element.online(attrs.isonline, attrs.onlinesize);

			$scope.$watch('contactsObj', function(){
				element.online(Chat.scope.contactsObj[attrs.id].online, attrs.onlinesize);
			});
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
	}
}).filter('userFilter', function() {
	return function(users) {
		var output = [];
		users.forEach(function(user, index){
			if(user.id !== Chat.scope.active.user.id){
				output.push(user);
			}
		});
		return output;
	}
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
});
