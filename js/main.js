Chat.angular = angular.module('myApp',[]),
Chat.angular.controller('ConvController', ['$scope', function($scope) {
	$scope.activeConv = null;
	$scope.convs = {}; // Get started with the existing conversations retrieved from the server via an ajax request
	$scope.startmsg = 'Start Chatting!';
	
	$scope.updateTitle = function(newTitle){
            $scope.title = newTitle;
	}
	
	$scope.sendChatMsg = function(){
            if (this.chatMsg != ''){
                $scope.addChatMsgToView($scope.activeConv, OC.currentUser, this.chatMsg,new Date().getTime() / 1000);
                Chat.api.command.sendChatMsg(this.chatMsg, $scope.activeConv, function(){});
                this.chatMsg = '';
                Chat.ui.scrollDown();
            }
	};
	
	$scope.newConv = function(){
            var userToInvite = prompt('Give the owncloud user name: ');
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
                    			$scope.addConvToView(newConvId, userToInvite);
                    		},
                    		function(errorMsg){
                    			if(errorMsg === 'USER-TO-INVITE-NOT-ONLINE'){
                    				Chat.ui.alert('The user you tried to invite isn\'t online, you already can send messages');// TODO
                    			} else if(errorMsg === 'USER-TO-INVITE-NOT-OC-USER'){
                    				Chat.ui.alert('The user you tried to invite isn\'t a valid owncloud user')
                    				// Leave the already joined conversation
                    				Chat.api.command.leave(newConvId, function(){});
                    			} 
                			}
                    		);
                });
            }
	}

	$scope.addChatMsgToView = function(convId, user, msg, timestamp){
            if (user === OC.currentUser){
                    align = 'right';
            } else {
                    align = 'left'
            }
            $scope.convs[convId].msgs.push({
                user : user,
                msg : msg,
                timestamp : timestamp,
                time : Chat.util.timeStampToDate(timestamp), 
                align: align,
            });
            setTimeout(function(){
                Chat.ui.applyAvatar(user);
            },1); // Give angular some time to apply the msg to scope
            // Edit tab title when the tab isn't active
            if(user !== OC.currentUser) {
                Chat.tabTitle = 'New msg from ' + user;
            }
	}	
	
	$scope.addConvToView = function(newConvId, convName){
            $scope.convs[newConvId] = {
                name : convName,
                id : newConvId,
                users : [
                         OC.currentUser,
                         convName
                         ],
                msgs : []
            };
            // Check if this is the first conversation
            if($('#empty-panel').is(":visible")){
                    Chat.ui.clear();
                    Chat.ui.showChat();
            }
            $scope.makeActive(newConvId);
	}
	
	$scope.makeActive = function(convId){
            $scope.activeConv = convId;
            Chat.ui.focusMsgInput();
            Chat.ui.markConvActive(convId);
	}
	
	$scope.leave = function(){
            var confirm = window.confirm("Are you sure you want to leave this conversation?");
            if (confirm === true) {
                delete $scope.convs[$scope.activeConv];
                Chat.api.command.leave($scope.activeConv);
                if(Chat.util.countObjects($scope.convs) === 0){
                    Chat.ui.clear();
                    Chat.ui.showEmpty();
                } else {
                        $scope.makeActive(Chat.ui.getFirstConv());
                }
            }
	}
	
	$scope.invite = function(){
		
		
		
		
            var userToInvite = prompt('Give the owncloud user name: ');
            if(userToInvite === OC.currentUser){
            	Chat.ui.alert('You can\'t invite yourself');
            } else if(userToInvite === ''){
                Chat.ui.alert('Please provide a user name');
            } else {
            	Chat.api.command.invite(
            		userToInvite,
            		$scope.activeConv,
            		function(){ // Success
            		},
            		function(errorMsg){
            			if(errorMsg === 'USER-TO-INVITE-NOT-ONLINE'){
            				Chat.ui.alert('The user you tried to invite isn\'t online, you already can send messages');// TODO
            			} else if(errorMsg === 'USER-TO-INVITE-NOT-OC-USER'){
            				Chat.ui.alert('The user you tried to invite isn\'t a valid owncloud user')
            			} 
        			}
        		);
            }
	};
	
	$scope.hideHeader = function(){
            height = $(window).height();
            if(height < 800 && Chat.util.checkMobile()){
                    Chat.ui.hideHeader();
            }
	};
	
	$scope.showHeader = function(){
            Chat.ui.showHeader();
	};
	
	$scope.focusMsgInput = function(){
            Chat.ui.focusMsgInput();
	}
}]);
	


