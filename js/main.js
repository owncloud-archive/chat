
Chat.angular = angular.module('myApp',[]),
Chat.angular.controller('ConvController', ['$scope', function($scope) {
	//alert('controller caleed');
	$scope.activeConv = null;
	$scope.convs = {}; // Get started with the existing conversations retrieved from the server via an ajax request
	$scope.msgs = {}; // Start with the last messages retrieved from the chat server
	$scope.startmsg = 'Start Chatting!';
	
	$scope.updateTitle = function(newTitle){
		$scope.title = newTitle;
	}
	
	$scope.sendChatMsg = function(){
		if (this.chatMsg != ''){
			$scope.convs[$scope.activeConv].msgs.push({
	        	user : OC.currentUser,
	        	msg : this.chatMsg,
	        	timestamp : 1387722204,
	        	time : Chat.util.timeStampToDate(new Date().getTime() / 1000), 
	        	align: 'right'
	        });
			this.chatMsg = '';
			// Do send msg magic here
			Chat.ui.scrollDown();
		}
	};
	
	$scope.newConv = function(){
		var userName = prompt('Give the owncloud user name: ');
		if(userName === OC.currentUser){
			alert('You can\'t start a conversation with yourself');
		} else if(userName === ''){
			alert('Please provide a user name');
		} else {
			var convId = Chat.util.generateConvId();
			$scope.convs[convId] = {
            	name : userName,
            	id : convId,
            	users : [
            	         OC.currentUser,
            	         userName
            	         ],
            	msgs : []
			};
			// Check if this is the first conversation
			if($('#empty-panel').is(":visible")){
				Chat.ui.clear();
				Chat.ui.showChat();
			}
			$scope.makeActive(convId);
			
		}
	}

	$scope.makeActive = function(convId){
		$scope.activeConv = convId;
		$scope.msgs = $scope.convs[convId].msgs; 
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
	
	$scope.simulate = function(){
		$scope.convs[$scope.activeConv].msgs.push({
        	user : 'Derpina',
        	msg : 'lpisasfjas;lkfja;lskdjf;lkasjdf;lajs;dflj',
        	timestamp : new Date().getTime() / 1000,
        	time : Chat.util.timeStampToDate(new Date().getTime() / 1000), 
        	align: 'left'
        });
		Chat.ui.scrollDown();
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
	


