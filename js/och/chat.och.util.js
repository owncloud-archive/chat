Chat.och.util = {
    init : function(){
        Chat.och.sessionId = Chat.och.util.generateSessionId();
        Chat.och.api.command.greet(function(){
            //TODO add getConversation function
            Chat.och.api.util.longPoll();
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