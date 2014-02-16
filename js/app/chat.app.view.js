Chat.app.view = {
    addConv : function(convId, users, backend){
        Chat.scope.$apply(function(){
            Chat.scope.view.addConv(convId, users, backend);
        });
    },
    addChatMsg : function(convId, user, msg, timestamp){
        Chat.scope.$apply(function(){
            Chat.scope.view.addChatMsg(convId, user, msg, timestamp); 
        });
    },
    addUserToConv : function(convId, user){
        Chat.scope.$apply(function(){
            Chat.scope.view.addUserToConv(convId, user); 
        });
    },
    getBackends : function(key){
    	var returnBackend;
    	Chat.scope.backends.forEach(function(backend, index){
    		console.log(backend);
    		if(key === backend.name){
    			returnBackend = backend;
    		}
    	});
    	return returnBackend;
    },
    alert : function(text){
        Chat.app.ui.alert(text);
    }
};