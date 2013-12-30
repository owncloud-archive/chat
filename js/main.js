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
		if(userToInvite === OC.currentUser){
			alert('You can\'t start a conversation with yourself');
		} else if(userToInvite === ''){
			alert('Please provide a user name');
		} else {
			var newConvId = Chat.util.generateConvId();
			Chat.api.command.join(newConvId, function(){ 
				Chat.api.command.invite(userToInvite, newConvId, function(){
					$scope.addConvToView(newConvId, userToInvite);
				});
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
        	align: align
        });
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
			if(Chat.util.countObjects($scope.convs) === 0){
				Chat.ui.clear();
				Chat.ui.showEmpty();
			} else {
				$scope.makeActive(Chat.ui.getFirstConv());
			}
		}
	}
	
	$scope.invite = function(){
		var userName = prompt('Give the owncloud user name: ');
		if(userName === OC.currentUser){
			alert('You can\'t invite yourself');
		} else if(userName === ''){
			alert('Please provide a user name');
		} else {
			// Do magic invite stuff here
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
	
}]);
	


