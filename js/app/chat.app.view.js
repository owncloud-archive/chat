Chat.app.view = {
	addConv : function(convId, users, backend, msgs){
		Chat.scope.$apply(function(){
			Chat.scope.view.addConv(convId, users, backend, msgs);
		});
	},
	addChatMsg : function(convId, contact, msg, timestamp, backend){
		Chat.scope.$apply(function(){
			Chat.scope.view.addChatMsg(convId, contact, msg, timestamp, backend);
		});
	},
	addUserToConv : function(convId, user){
		Chat.scope.$apply(function(){
			Chat.scope.view.addUserToConv(convId, user);
		});
	},
	getBackends : function(key){
		var returnBackend;
		angular.forEach(Chat.scope.backends, function(backend, index){
			if(key === backend.name){
				returnBackend = backend;
			}
		});
		return returnBackend;
	},
	alert : function(text){
		Chat.app.ui.alert(text);
	},
	replaceUsers : function(convId, users){
		Chat.scope.$apply(function(){
	 		Chat.scope.view.replaceUsers(convId, users);
		});
	}
};