Chat.angular.controller('ConvController', ['$scope', '$filter', function($scope, $filter) {
    $scope.convs = {}; 
    $scope.contacts = [];
    $scope.contactsList = [];
    $scope.backends = [];
    $scope.active = {
        backend : {},
        conv : {},
        //user :
    };
    $scope.headerInfo = "Chose a contact to conversate with. Select a backend in the right bottom";
    // $scope.userToInvite // generated in main.php // this is the user to invite in the new conv panel
	
    $scope.init = function(){
    	var initvar = JSON.parse($('#initvar').text());
    	console.log(initvar);
    	$scope.contacts = initvar['contacts'];
        $scope.contactsList = initvar['contactsList'];
       	$scope.backends = initvar['backends'];
       	$scope.active.user = initvar['currentUser'];
       	for (active in $scope.backends) break;
    	$scope.active.backend =  $scope.backends[active];
    	$scope.$apply();
        angular.forEach($scope.backends, function(backend, namespace){
            if(namespace === 'och'){
                Chat[namespace].util.init();
            }
        });
        $scope.initDone = true;
    };
    
    $scope.selectBackend = function(backend){
      	$scope.active.backend = backend;
    };
    
    $scope.view = {
        elements : {
            "contact" : true,
            "chat" : false,
    	    "initDone" : false,
    	    "settings" : false,
    	    "invite" : false
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
	    addConv : function(convId, users, backend){
	    	users.push($scope.active.user);
	    	$scope.convs[convId] = {
                id : convId,
                users : users,
                msgs : [],
                backend : backend,
            };
            $scope.view.makeActive(convId);
            $scope.$apply();
	    },
	    addChatMsg : function(convId, user, msg, timestamp, backend){
            // Check if the user is equal to the user of the last msg
            // First get the last msg
	    	var contact = user;
	    	
            if($scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1] !== undefined){
                var lastMsg = $scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1];
                if(lastMsg.contact.displayname === contact.displayname){
                    lastMsg.msg = lastMsg.msg + "<br>" + $.trim(msg);
                    $scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1] = lastMsg;
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
    	    $scope.convs[convId].users.push(user);    
    	},
        focusMsgInput : function(){
            Chat.app.ui.focusMsgInput();
	    },
    };
    
    	
    $scope.sendChatMsg = function(){
        if (this.chatMsg != ''){
            var backend = $scope.convs[$scope.active.conv].backend.name;
            $scope.view.addChatMsg($scope.active.conv, $scope.active.user, this.chatMsg,new Date().getTime() / 1000, backend);
            Chat[backend].on.sendChatMsg($scope.active.conv, this.chatMsg);
            this.chatMsg = '';
        }
    };
	
    $scope.newConv = function(userToInvite){
    	var backend = $scope.active.backend;
        if(userToInvite === $scope.active.user){
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
	
}]).directive('avatar', function() {
    return {    
        restrict: 'A',
        link: function ($scope, element, attrs) {
            element.applyContactAvatar(attrs.addressbookBackend, attrs.addressbookId, attrs.id, attrs.displayname, attrs.size);
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
    return function(users, activeUser) {
        var output = [];
        users.forEach(function(user, index){
            if(user !== activeUser){
                output.push(user);
            }
        });
        return output;
    }
});