Chat.och.util = {
    init : function(){
    	console.log('initing och');
        Chat.och.sessionId = Chat.och.util.generateSessionId();
        Chat.och.api.command.greet(function(){
            //TODO add getConversation function
            Chat.och.api.util.longPoll();
        });
    },
    quit : function(){
       
    },
	generateConvId : function(){
        var timestamp = "conversation" + (new Date).getTime();
        var conversationID = md5(timestamp);
        return conversationID.toString();
    },
    generateSessionId : function(){
        var timestamp = "sessionID" + (new Date).getTime();
        var sessionID = md5(timestamp);
        return sessionID.toString();
    } 
};