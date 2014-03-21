Chat.och.api = {
    command : {
        greet : function(success){
            Chat.och.api.util.doRequest({"type" : "command::greet::request", "data" : { "user" : Chat.scope.active.user, "session_id" : Chat.och.sessionId, "timestamp" : (new Date).getTime() / 1000 }}, success);
        },
        join : function(convId, success){
            Chat.och.api.util.doRequest({"type" : "command::join::request", "data" : { "conv_id": convId, "timestamp" : (new Date).getTime() / 1000, "user" : Chat.scope.active.user, "session_id" : Chat.och.sessionId }}, success);
        },
        invite : function(userToInvite, convId, success, error){
            Chat.och.api.util.doRequest({"type" : "command::invite::request", "data" : { "conv_id" : convId, "timestamp" : (new Date).getTime() / 1000, "user_to_invite" : userToInvite , "user" : Chat.scope.active.user, "session_id" : Chat.och.sessionId }},success,error);
        },
        sendChatMsg : function(msg, convId, success){
            Chat.och.api.util.doRequest({"type" : "command::send_chat_msg::request", "data" : {"conv_id" : convId, "chat_msg" : msg, "user" : Chat.scope.active.user, "session_id" : Chat.och.sessionId, "timestamp" : (new Date).getTime() / 1000 }}, success);
        },
        online : function(success){
            Chat.och.api.util.doRequest({"type" : "command::online::request", "data" : { "user" : Chat.scope.active.user,
                    "session_id" : Chat.och.sessionId, "timestamp" : (new Date).getTime() / 1000}}, function(){});
        },
        startConv : function(userToInvite, success){
            Chat.och.api.util.doRequest({
                "type" : "command::start_conv::request",
                "data" : {
                    "user" : Chat.scope.active.user,
                    "session_id": Chat.och.sessionId,
                    "timestamp" : (new Date).getTime() / 1000,
                    "user_to_invite" : userToInvite
                }
            }, success);
        }
    },
    on : {
        invite : function(data){
            // Here update the view
        	var backend = Chat.app.view.getBackends('och');
            Chat.app.view.addConv(data.conv_id, [data.user], backend);
            Chat.och.api.command.join(data.conv_id, function(){});
            // TODO move this to the concontroller
            Chat.app.ui.alert('You auto started a new conversation with ' + data.user);
        },
        chatMessage : function(data){
        	Chat.app.view.addChatMsg(data.conv_id, data.user, data.chat_msg, data.timestamp, 'och');
        },
        joined : function(data){
            // TODO move this alert to convcontroller
            Chat.app.ui.alert('The user ' + data.user + ' joined this conversation');
            Chat.app.view.addUserToConv(data.conv_id, data.user);           
        }
    },
    util : {
        doRequest : function(request, success, error){
            $.post('/index.php' + OC.linkTo("chat", "och/api"), {JSON: JSON.stringify(request)}).done(function(response){
             if(response.data.status === "success"){
                    success(response);
                } else if (response.data.status === "error"){
                 error(response.data.data.msg);
                }
            });
        },
        longPoll : function(){
            this.getPushMessages(function(push_msgs){
                var ids_del = [];
                $.each(push_msgs.push_msgs, function(push_id, push_msg){
                    ids_del.push(push_id);
                    Chat.och.api.util.handlePushMessage(push_msg);
                });
                Chat.och.api.util.deletePushMessages(ids_del, function(){
                    Chat.och.api.util.longPoll();
                });
            });
        },
        handlePushMessage : function(push_msg){
            if (push_msg.type === "invite"){
                Chat.och.api.on.invite(push_msg.data);
            } else if (push_msg.type === "send_chat_msg"){
                Chat.och.api.on.chatMessage(push_msg.data);
            } else if (push_msg.type === "joined"){
                Chat.och.api.on.joined(push_msg.data);
            } /*else if (msg.data.type === "left"){
    var conversationID = msg.data.param.conversationID;
    getUsers(server, conversationID, function(msg){
    if (msg.data.param.users.length <= 1){
    deleteConversation(conversationID);
    }
    });
    }*/
        },
        getPushMessages : function(success){
            $.post('/index.php' + OC.linkTo("chat", "och/api"), {"JSON" : JSON.stringify({"type" : "push::get::request", "data" : { "user" : Chat.scope.active.user, "session_id" : Chat.och.sessionId}})}, function(data){
                success(data);
            });
        },
        deletePushMessages : function(ids, success){
            $.post('/index.php' + OC.linkTo("chat", "och/api"), {"JSON" : JSON.stringify({"type" : "push::delete::request", "data" : {"user" : Chat.scope.active.user, "session_id" : Chat.och.sessionId, ids: ids}})}, function(data){
               success();
            });
        },
    }
};