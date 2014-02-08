Chat.angular.controller('ConvController', ['$scope', 'contacts',function($scope, contacts) {
	$scope.activeConv = null;
	$scope.convs = {}; 
	$scope.currentUser = OC.currentUser;
	$scope.debug = [];
	$scope.contacts = [];
	$scope.contactsList = [];
    
    $scope.init = function(){
        contacts(function(data){
            $scope.contacts = data['contacts'];
            $scope.contactsList = data['contactsList'];
            $scope.$apply();
        });
        // TOOD init every backend 
        $scope.initDone = true;
        angular.forEach(Chat.app.backends, function(backend, key){
            // For each backend call the init function of the Chat.{backend}.util.init() function
            console.log(backend);
            Chat[backend].util.init();
        });
    }
    
    $scope.view = {
        elements : {
            "inviteInput" : false,
	        "contact" : true,
	        "chat" : false,
    	    "initDone" : false,
        },        
        show : function(element){
            console.log('show' + element);
            $scope.view.elements[element] = true;
        },
        hide : function(element){
            $scope.view.elements[element] = false;
        },
        toggle : function(element){
            $scope.view.elements[element] = !$scope.view.elements[element];
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
                currentUser : OC.currentUser
            };
            $scope.view.show('chat');
            $scope.view.makeActive(convId);
            //Chat.app.ui.applyAvatar(convName); // TODO
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
                Chat.app.ui.applyAvatar(user);
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
            // TODO Chat[backned].on.sendChatMsg(this.chatMsg);
            Chat.och.on.sendChatMsg($scope.activeConv, this.chatMsg);
            this.chatMsg = '';
        }
	};
	
    $scope.newConv = function(userToInvite){
        if(userToInvite.toLowerCase() === OC.currentUser.toLowerCase()){
        	Chat.app.ui.alert('You can\'t start a conversation with yourself');
        } else if(userToInvite === ''){
        	Chat.app.ui.alert('Please provide an ownCloud user name');
        } else {
            // TODO Chat[backend].on.newConv(userToInvite);
            Chat.och.on.newConv(userToInvite, function(convId, users){
                $scope.view.addConv(convId, users);
            });
        }
        this.userToInvite = '';
	};
	$scope.leave = function(convId){
	    // TODO Chat[backend].on.leave(convId);
        delete $scope.convs[convId];
        if(Chat.util.countObjects($scope.convs) === 0){
            $scope.hide('chat');
            $scope.view.show('contact');
        } else {
            $scope.view.makeActive(Chat.app.ui.getFirstConv());
        }    
	};
	
	$scope.invite = function(userToInvite, convId){
        if(userToInvite === OC.currentUser){
        	Chat.app.ui.alert('You can\'t invite yourself');
        } else if(userToInvite === ''){
            Chat.app.ui.alert('Please provide a user name');
        } else {
            // TODO Chat[backend].on.invite(convId, userToInvite);
        }
        $scope.view.hide('inviteInput');
	};
	
}]).factory('contacts', function() {
    return function(callback){
        $.get(OC.Router.generate("chat_get_contacts")).then(callback);
    }
});