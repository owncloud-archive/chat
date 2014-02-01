Chat.angular = angular.module('myApp',['ngSanitize']);
Chat.angular.controller('ConvController', ['$scope', function($scope) {
	$scope.activeConv = null;
	$scope.convs = {}; // Get started with the existing conversations retrieved from the server via an ajax request
	$scope.startmsg = 'Start Chatting!';
	$scope.currentUser = OC.currentUser;
	$scope.debug = [];
    
    $scope.popover = {
        button : false,
        title : "World"
    };

	$scope.updateTitle = function(newTitle){
            $scope.title = newTitle;
	}
	
	$scope.sendChatMsg = function(){
            if (this.chatMsg != ''){
                $scope.addChatMsgToView($scope.activeConv, OC.currentUser, this.chatMsg,new Date().getTime() / 1000);
                Chat.api.command.sendChatMsg(this.chatMsg, $scope.activeConv, function(){});
                this.chatMsg = '';
            }
	};
	
	$scope.newConvShow = function(){
        $scope.popover.title = "Fill in  an ownCoud username";
        $scope.popover.placeholder = "ownCloud username";
        $scope.popover.submit = "Invite";
        $scope.popover.top = -14;
        $scope.popover.action = function(value){
            $scope.newConv(value);
        }
        $scope.popover.button = false;
        setTimeout(function(){
            Chat.ui.showPopover();
	        Chat.ui.focus('#popover-value');
        },1); // Give angular some time to apply the msg to scope
	};
	
	$scope.inviteShow = function(convId){
        $scope.popover.title = "Fill in  an ownCoud username";
        $scope.popover.placeholder = "ownCloud username";
        $scope.popover.submit = "Invite";
        $scope.popover.top = -14 + (Chat.ui.getConvIndex(convId) - 1 ) * 70;
        $scope.popover.action = function(value){
            $scope.invite(convId, value);
        };
        $scope.popover.button = false;
        setTimeout(function(){
            Chat.ui.showPopover();
	        Chat.ui.focus('#popover-value');
        },1); // Give angular some time to apply the msg to scope

	};
	
	$scope.leaveShow = function(convId){
        $scope.popover.title = "Are you sure you want to leave this conversation?";
        $scope.popover.top = -14 + (Chat.ui.getConvIndex(convId) - 1 ) * 70;
        $scope.popover.action = function(){
            $scope.leave(convId);
        };
        $scope.popover.button = true;
        setTimeout(function(){
            Chat.ui.showPopover();
	        Chat.ui.focus('#popover-value');
        },1); // Give angular some time to apply the msg to scope
 
	};
	
	
	$scope.popoverSubmit = function(type){
	    if(type === 'submit'){
	        $scope.popover.action(this.popover.value);
	    } else if (type === 'yes'){
	        $scope.popover.action();
	    }
        Chat.ui.hidePopover();
	};
	
    $scope.newConv = function(userToInvite){
        console.log('wooto');
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
	};

	$scope.addChatMsgToView = function(convId, user, msg, timestamp){
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
	};
	
	$scope.addConvToView = function(newConvId, convName){
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
            // Check if this is the first conversation
            if($('#empty-window').is(":visible")){
                Chat.ui.hideEmpty();
                Chat.ui.showChat();
            }
            $scope.makeActive(newConvId);
            Chat.ui.applyAvatar(convName);
	};
	
	$scope.makeActive = function(convId){
            $scope.activeConv = convId;
            Chat.ui.focusMsgInput();
            Chat.ui.markConvActive(convId);
	};
	
	$scope.leave = function(convId){
        delete $scope.convs[convId];
        Chat.api.command.leave(convId);
        if(Chat.util.countObjects($scope.convs) === 0){
            Chat.ui.clear();
            Chat.ui.showEmpty();
        } else {
            $scope.makeActive(Chat.ui.getFirstConv());
        }
	};
	
	$scope.invite = function(convId, userToInvite ){
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
	};
	
	$scope.focusMsgInput = function(){
		Chat.ui.focusMsgInput();
	};
	
	$scope.submit = function(){
	    console.log('ai caramabe');
	}
}]);

Chat.angular.directive('popover', function($templateCache, $http, $q, $compile) {
    return function(scope, element, attrs) {
        $http.get(attrs.templateUrl).success(function(data, status){
            var contentTemplate = data;
            
            var template = angular.element(
                '<div id="popover" class="popover right animation-fade" style="top: ' + attrs.top  +'px; left: 300px;">' +
                    '<div class="arrow"></div>' +
                    '<h3 class="popover-title">' + attrs.title + '<span ng-click="hide()" style="float:right"> Close</span></h3>' +
                    '<div class="popover-content">' +
                        contentTemplate + 
                    '</div>' +
                '</div>');
        
            var compiled = $compile(template);
            
            element.after(template);
    
            compiled(scope);
        });
            
        scope.submit = function() {
            scope.newConv(this.value);
            this.value = '';
            scope.hide();
        };
            
        scope.hide = function(){
            element.next().fadeOut();
        };
            
        scope.show = function(){
            element.next().fadeIn();
        }

    };
});
	


