Chat.angular.controller('ConvController', ['$scope', 'contacts',function($scope, contacts) {
	$scope.activeConv = null;
	$scope.convs = {}; // Get started with the existing conversations retrieved from the server via an ajax request
	$scope.startmsg = 'Start Chatting!';
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
        Chat.util.init(); 
        $scope.initDone = true;
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
	    addConv : function(newConvId, convName){
    	    console.log('addoncvotieg');
            $scope.convs[newConvId] = {
                name : convName,
                id : newConvId,
                users : [
                         OC.currentUser,
                         convName
                         ],
                msgs : [],
                currentUser : OC.currentUser
            };
            $scope.view.show('chat');
            $scope.view.makeActive(newConvId);
            Chat.ui.applyAvatar(convName);
    		$scope.$apply();
	    },
	    addChatMsg : function(convId, user, msg, timestamp){
            if (user === OC.currentUser){
                align = 'right';
            } else {
                align = 'left'
            }
            
            // Check if the user is equal to the user of the last msg
            // First get the last msg
            if($scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1] !== undefined){
                var lastMsg = $scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1];
                if(lastMsg.user === user){
                    lastMsg.msg = lastMsg.msg + "<br>" + $.trim(msg);
                    $scope.convs[convId].msgs[$scope.convs[convId].msgs.length -1] = lastMsg;
                } else if (Chat.util.timeStampToDate(lastMsg.timestamp).minutes === Chat.util.timeStampToDate(timestamp).minutes
                            && Chat.util.timeStampToDate(lastMsg.timestamp).hours === Chat.util.timeStampToDate(timestamp).hours
                            ) {
                    $scope.convs[convId].msgs.push({
                        user : user,
                        msg : $.trim(msg),
                        timestamp : timestamp,
                        time : null, 
                        align: align,
                    });    
                } else {
                     $scope.convs[convId].msgs.push({
                        user : user,
                        msg : $.trim(msg),
                        timestamp : timestamp,
                        time : Chat.util.timeStampToDate(timestamp), 
                        align: align,
                    });     
                }
            } else {
                $scope.convs[convId].msgs.push({
                    user : user,
                    msg : $.trim(msg),
                    timestamp : timestamp,
                    time : Chat.util.timeStampToDate(timestamp), 
                    align: align,
                });
            }
            
            setTimeout(function(){
                Chat.ui.applyAvatar(user);
                Chat.ui.scrollDown();
            },1); // Give angular some time to apply the msg to scope
            // Edit tab title when the tab isn't active
            if(user !== OC.currentUser) {
                Chat.tabTitle = 'New msg from ' + user;
            }
    	},
        focusMsgInput : function(){
            Chat.ui.focusMsgInput();
	    },
    };
    
    	
	$scope.sendChatMsg = function(){
        if (this.chatMsg != ''){
            $scope.view.addChatMsg($scope.activeConv, OC.currentUser, this.chatMsg,new Date().getTime() / 1000);
            Chat.api.command.sendChatMsg(this.chatMsg, $scope.activeConv, function(){});
            this.chatMsg = '';
        }
	};
	
    $scope.newConv = function(userToInvite){
        //if(backend === "ownCloud"){
            if(userToInvite.toLowerCase() === OC.currentUser.toLowerCase()){
            	Chat.ui.alert('You can\'t start a conversation with yourself');
            } else if(userToInvite === ''){
            	Chat.ui.alert('Please provide an ownCloud user name');
            } else {
                var newConvId = Chat.util.generateConvId();
                Chat.api.command.join(newConvId, function(){ 
                    Chat.api.command.invite(
                    		userToInvite,
                    		newConvId,
                    		function(){ // Success
                    			$scope.view.addConv(newConvId, userToInvite);
                    		},
                    		function(errorMsg){
                    			if(errorMsg === 'USER-TO-INVITE-NOT-ONLINE'){
                    				Chat.ui.alert('The user you tried to invite isn\'t online, you already can send messages');// TODO
                    			} else if(errorMsg === 'USER-TO-INVITE-NOT-OC-USER'){
                    				Chat.ui.alert('The user you tried to invite isn\'t a valid owncloud user')
                    				// Leave the already joined conversation
                    				Chat.api.command.leave(newConvId, function(){});
                    			} else {
                    				Chat.ui.alert(errorMsg);
                    			}
                			}
                    		);
                });
            }
       // } else {
        //    alert("Unsupported");
       // }
        this.userToInvite = '';
	};

	
	

	

	
	$scope.leave = function(convId){
        Chat.api.command.leave(convId, function(){
            delete $scope.convs[convId];
            if(Chat.util.countObjects($scope.convs) === 0){
                $scope.hide('chat');
                $scope.view.show('contact');
            } else {
                $scope.view.makeActive(Chat.ui.getFirstConv());
            }    
        });
        Chat.ui.alert();
	};
	
	$scope.invite = function(userToInvite, convId){
        if(userToInvite === OC.currentUser){
        	Chat.ui.alert('You can\'t invite yourself');
        } else if(userToInvite === ''){
            Chat.ui.alert('Please provide a user name');
        } else {
        	Chat.api.command.invite(
        		userToInvite,
        		convId,
        		function(){ // Success
        		},
        		function(errorMsg){ // Error
        			if(errorMsg === 'USER-TO-INVITE-NOT-ONLINE'){
        				Chat.ui.alert('The user you tried to invite isn\'t online, you already can send messages');// TODO
        			} else if(errorMsg === 'USER-TO-INVITE-NOT-OC-USER'){
        				Chat.ui.alert('The user you tried to invite isn\'t a valid owncloud user')
        			} 
    			}
    		);
        }
        $scope.view.hide('inviteInput');
	};
	

	
}]).factory('contacts', function() {
    return function(callback){
        $.get(OC.Router.generate("chat_get_contacts")).then(callback);
    }
});