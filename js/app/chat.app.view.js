Chat.app.view = {
    addConv : function(convId, users){
        Chat.scope.$apply(function(){
            Chat.scope.view.addConv(convId, users);
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
    alert : function(text){
        Chat.app.ui.alert(text);
    }
};