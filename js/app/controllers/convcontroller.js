Chat.angular.controller('ConvController', ['$scope', 'contacts', 'backends',function($scope, contacts, backends) {
	$scope.activeConv = null;
	$scope.convs = {}; 
	$scope.currentUser = OC.currentUser;
	$scope.debug = [];
	$scope.contacts = [];
	$scope.contactsList = [];
	$scope.backends = [];
	// $scope.userToInvite // generated in main.php // this is the user to invite in the new conv panel
	//$scope.selectedBackend // set in $scope.init // this is the selected backend in the backend choser in the new conv panel
	
    $scope.init = function(){
        contacts(function(data){
            $scope.contacts = data['contacts'];
            $scope.contactsList = data['contactsList'];
            $scope.$apply();
        });
        backends(function(data){
        	$scope.backends = data['backends'];
        	$scope.selectedBackend = $scope.backends[0];
        });
        // TOOD init every backend 
        angular.forEach(Chat.app.backends, function(backend, key){
            // For each backend call the init function of the Chat.{backend}.util.init() function
            Chat[backend].util.init();
        });
        $scope.initDone = true;
    }
    
    $scope.view = {
        elements : {
            "inviteInput" : false,
	        "contact" : true,
	        "chat" : false,
    	    "initDone" : false,
    	    "backendSelect" : false
        },        
        show : function(element, $event){
            $scope.view.elements[element] = true;
            if ($event != null) {
                if (typeof $event.stopPropagation === "function") {
                  $event.stopPropagation();
                }
        	}
        },
        hide : function(element){
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
            $scope.activeConv = convId;
            $scope.view.focusMsgInput();
	    },
	    addConv : function(convId, users){
	    	$scope.convs[convId] = {
                id : convId,
                users : users,
                msgs : [],
                currentUser : OC.currentUser,
                backend : null,
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
            if(user !== OC.currentUser) {
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
            $scope.view.addChatMsg($scope.activeConv, OC.currentUser, this.chatMsg,new Date().getTime() / 1000);
            var backend = $scope.convs[$scope.activeConv].backend;
            Chat[backend].on.sendChatMsg($scope.activeConv, this.chatMsg);
            this.chatMsg = '';
        }
	};
	
    $scope.newConv = function(userToInvite, backend){
        if(userToInvite.toLowerCase() === OC.currentUser.toLowerCase()){
        	Chat.app.ui.alert('You can\'t start a conversation with yourself');
        } else if(userToInvite === ''){
        	Chat.app.ui.alert('Please provide an ownCloud user name');
        } else {
        	Chat[backend].on.newConv(userToInvite, function(convId, users){
                $scope.view.addConv(convId, users);
            });
        }
        this.userToInvite = '';
	};
	
	$scope.leave = function(convId){
        var backend = $scope.convs[convId].backend;
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
        var backend = $scope.convs[convId].backend;
        if(userToInvite === OC.currentUser){
        	Chat.app.ui.alert('You can\'t invite yourself');
        } else if(userToInvite === ''){
            Chat.app.ui.alert('Please provide a user name');
        } else {
            Chat[backend].on.invite(convId, userToInvite);
        }
        $scope.view.hide('inviteInput');
	};
	
}]).factory('contacts', function() {
    return function(callback){
        $.get(OC.Router.generate("chat_get_contacts")).then(callback);
    };
}).factory('backends', function() {
    return function(callback){
        $.get(OC.Router.generate("chat_get_backends")).then(callback);
    };
}).directive('avatar', function() {
    return {    
        restrict: 'A',
        link: function (scope, element,attrs) {
            element.avatar(attrs.user, attrs.size);
        }
    };
});;