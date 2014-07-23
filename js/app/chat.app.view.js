Chat.app.view = {
	addConv : function(convId, users, backend, msgs, archived){
		Chat.scope.$apply(function(){
			Chat.scope.view.addConv(convId, users, backend, msgs, archived);
		});
	},
	addChatMsg : function(convId, contact, msg, timestamp, backend, noNotify){
		Chat.scope.$apply(function(){
			Chat.scope.view.addChatMsg(convId, contact, msg, timestamp, backend, noNotify);
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
	replaceUsers : function(convId, users){
		Chat.scope.$apply(function(){
	 		Chat.scope.view.replaceUsers(convId, users);
		});
	},
	makeActive : function(convId, $event, exception){
		Chat.scope.$apply(function(){
			Chat.scope.view.makeActive(convId, $event, exception);
		});
	}
};