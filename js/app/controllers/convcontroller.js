Chat.angular.controller('ConvController', ['$scope',function($scope) {
	$scope.convs = {}; 
	$scope.contacts = [];
	$scope.contactsList = [];
	$scope.backends = [];
	$scope.active = {
		backend : {},
		conv : {},
		user : OC.currentUser
	};
	// $scope.userToInvite // generated in main.php // this is the user to invite in the new conv panel
	
	
	
    $scope.init = function(){
    	var initvar = JSON.parse($('#initvar').text());
    	console.log(initvar);
    	$scope.contacts = initvar['contacts'];
        $scope.contactsList = initvar['contactsList'];
       	$scope.backends = initvar['backends'];
    	$scope.active.backend = $scope.backends[0];
    	$scope.$apply();
        	// TOOD init every backend 
        angular.forEach(initvar['backends'], function(backend, key){
            // For each backend call the init function of the Chat.{backend}.util.init() function
        	if(backend.name === 'och'){
        	Chat[backend.name].util.init();
        	}
        });
     
        $scope.initDone = true;
    };
    
    $scope.selectBackend = function(backend){
      	$scope.active.backend = backend;
    };
    
    $scope.view = {
        elements : {
            "inviteInput" : false,
	        "contact" : true,
	        "chat" : false,
    	    "initDone" : false,
    	    "settings" : false,
        },        
        show : function(element, $event){
            $scope.view.elements[element] = true;
            if ($event != null) {
                if (typeof $event.stopPropagation === "function") {
                  $event.stopPropagation();
                }
        	}
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
        	if ($event != null) {
                if (typeof $event.stopPropagation === "function") {
                  $event.stopPropagation();
                }
        	}
        },
    	updateTitle : function(newTitle){
            $scope.title = newTitle;
    	},
    	makeActive : function(convId){
            $scope.active.conv = convId;
            $scope.view.focusMsgInput();
	    },
	    addConv : function(convId, users, backend){
	    	$scope.convs[convId] = {
                id : convId,
                users : users,
                msgs : [],
                backend : backend,
            };
            $scope.view.show('chat');
            $scope.view.makeActive(convId);
            $scope.$apply();
	    },
	    addChatMsg : function(convId, user, msg, timestamp){
            // Check if the user is equal to the user of the last msg
            // First get the last msg
            if($scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1] !== undefined){
                var lastMsg = $scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1];
                if(lastMsg.user === user){
                    lastMsg.msg = lastMsg.msg + "<br>" + $.trim(msg);
                    $scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1] = lastMsg;
                } else if (Chat.app.util.timeStampToDate(lastMsg.timestamp).minutes === Chat.app.util.timeStampToDate(timestamp).minutes
                            && Chat.app.util.timeStampToDate(lastMsg.timestamp).hours === Chat.app.util.timeStampToDate(timestamp).hours
                            ) {
                    $scope.convs[convId].msgs.push({
                        user : user,
                        msg : $.trim(msg),
                        timestamp : timestamp,
                        time : null, 
                    });    
                } else {
                     $scope.convs[convId].msgs.push({
                        user : user,
                        msg : $.trim(msg),
                        timestamp : timestamp,
                        time : Chat.app.util.timeStampToDate(timestamp), 
                    });     
                }
            } else {
                $scope.convs[convId].msgs.push({
                    user : user,
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
    	    $scope.convs[convId].users.push(user);    
    	},
        focusMsgInput : function(){
            Chat.app.ui.focusMsgInput();
	    },
    };
    
    	
	$scope.sendChatMsg = function(){
	    if (this.chatMsg != ''){
            $scope.view.addChatMsg($scope.active.conv, $scope.active.user, this.chatMsg,new Date().getTime() / 1000);
            var backend = $scope.convs[$scope.active.conv].backend.name;
            Chat[backend].on.sendChatMsg($scope.active.conv, this.chatMsg);
            this.chatMsg = '';
        }
	};
	
    $scope.newConv = function(userToInvite){
    	var backend = $scope.active.backend;
        if(userToInvite.toLowerCase() === $scope.active.user.toLowerCase()){
        	Chat.app.ui.alert('You can\'t start a conversation with yourself');
        } else if(userToInvite === ''){
        	Chat.app.ui.alert('Please provide an ownCloud user name');
        } else {
        	Chat[backend.name].on.newConv(userToInvite, function(convId, users){
                $scope.view.addConv(convId, users, backend);
            });
        }
        this.userToInvite = '';
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
	
	$scope.invite = function(userToInvite, convId){
        var backend = $scope.convs[convId].backend.name;
        if(userToInvite === $scope.active.user){
        	Chat.app.ui.alert('You can\'t invite yourself');
        } else if(userToInvite === ''){
            Chat.app.ui.alert('Please provide a user name');
        } else {
            Chat[backend].on.invite(convId, userToInvite);
        }
        $scope.view.hide('inviteInput');
	};
	
}]).directive('avatar', function() {
    return {    
        restrict: 'A',
        link: function ($scope, element, attrs) {
        	var backendName;
        	if(attrs.backendName === undefined){
	        	if($scope.$parent.conv !== undefined){
	        		backendName = $scope.$parent.conv.backend.name;
	        	} else {
	        		backendName = $scope.$parent.$parent.$parent.convs[$scope.$parent.$parent.$parent.active.conv].backend.name;
	        	}
        	} else {
        		console.log('undefiend');
        		console.log(attrs);
        		backendName = attrs.backendName;
        	}
        		
        	//console.log($scope.$parent.conv.backend.name);
        	Chat[backendName].on.applyAvatar(element, attrs.user, attrs.size);
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
			if(backend.protocol === 'email'){
				if(contact.email.length > 0){
					output.push(contact);
				}
			} else {
				contact.IMPP.forEach(function(protocol){
					if(protocol.backend === backend.protocol){
						output.push(contact);
					}
				});
			}
			
		});
		return output;
	}
});