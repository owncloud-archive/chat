Chat.och.util = {
    init : function(){
        Chat.och.sessionId = Chat.och.util.generateSessionId();
        Chat.och.api.command.greet(function(){
            Chat.och.api.util.longPoll();
        });
        // Now join and add all the existing convs
        angular.forEach(Chat.scope.initConvs.och, function(conv){
            console.log('auto joining ' + conv);
            Chat.app.view.addConv(conv.id, conv.users, 'och');

        });
    },
    quit : function(){
       
    },
    generateSessionId : function(){
        var timestamp = "sessionID" + (new Date).getTime();
        var sessionID = md5(timestamp);
        return sessionID.toString();
    } 
};