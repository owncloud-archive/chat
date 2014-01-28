var Chat = {
/*  sessionId = '',
 */
	tabActive : true, 
	tabTitle : 'Chat - Owncloud',
	ui : {
    	clear : function(){
    		$('.panel').hide();
    	},
    	markConvActive : function(convId){
    		$('.conv-list-item').removeClass('conv-list-active');
    		$('#conv-list-' + convId).addClass('conv-list-active');
		},
    	scrollDown : function(){
    		$('#chat-window-msgs').scrollTop($('#chat-window-msgs')[0].scrollHeight);
    	},
    	showLoading : function(){
    		$('#loading-panel').show();
    	}, 
    	showEmpty : function(){
    		$('#empty-window').show();
    		this.focus('#empty-panel-new-conv');
    	},
    	showMain : function(){
    		$('#main-panel').show();
    	},
    	showChat : function(){
    		$('#chat-window').show();
    	},
    	focus : function(element){
    		$(element).focus();
    	},
    	focusMsgInput : function(){
    		if(!Chat.util.checkMobile()) {
    			Chat.ui.focus('#chat-msg-input');
    		}
    	},
    	getFirstConv : function(){
    		id = $("#app-navigation li:nth-child(2)").attr('id'); // conv-id-...
    		convId = id.substr(10, id.length); // strip conv-id-
    		return convId;
    	},
    	newMsg : function(convId){
    		$('#conv-new-msg-' + convId).fadeIn();
    		$('#conv-new-msg-' + convId).fadeOut();
    		this.newMsg(convId);
    	},
    	applyAvatar : function(user){
    	    $('.icon-' + user).avatar(user, 32);
    	},
    	updateTitle : function(){
    		if(!Chat.tabActive){
    			$('title').text(Chat.tabTitle);
    		}
		},
    	clearTitle : function(){
			$('title').text('Chat - ownCloud');
    	},
    	alert : function(text){
    		OC.Notification.showHtml(text);
			setTimeout(function(){
                OC.Notification.hide();
			}, 2000);
    	}
         
    },
    util : {
    	generateSessionId : function(){
            var timestamp = "sessionID" + (new Date).getTime();
            var sessionID = md5(timestamp);
            return sessionID.toString();
    	},
    	generateConvId : function(){
            var timestamp = "conversation" + (new Date).getTime();
            var conversationID = md5(timestamp);
            return conversationID.toString();
    	},
    	timeStampToDate : function(timestamp){
            var date = new Date(timestamp*1000);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();
            return	{hours : date.getHours(), minutes : date.getMinutes(), seconds : date.getSeconds()};
    	},
    	countObjects : function(object){
            var count = 0;
            for (var k in object) {
                if (object.hasOwnProperty(k)) {
                   ++count;
                }
            }
            return count;
    	},
    	checkMobile : function(){
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    	},
    	quit : function(){
            $.ajax({
                type: "POST",
                url: OC.Router.generate("chat_api"),
                data: {"JSON" : JSON.stringify({ "type" : "command::quit::request", "data" : { "user" : OC.currentUser, "session_id" : Chat.sessionId, "timestamp" : (new Date).getTime() / 1000}})},
                async: false,
            });
    	},
        init : function(){
            Chat.scope = angular.element($("#chat-wrapper")).scope();
            Chat.ui.updateTitle();
            Chat.ui.showLoading();
            Chat.sessionId = Chat.util.generateSessionId();
            Chat.api.command.greet(function(){
                //TODO add getConversation function
                Chat.ui.clear();
                Chat.ui.showMain();
                Chat.ui.showEmpty();
                Chat.api.util.longPoll();
            });
        },
        updateOnlineStatus : function(){
        	Chat.api.command.online();
        },
        titleHandler : function(){
        	Chat.ui.clearTitle();
    		setTimeout(function(){
    			Chat.ui.updateTitle();
    		}, 1000);
        }

    },
    api : {
    	command : {
            greet : function(success){
                Chat.api.util.doRequest({"type" : "command::greet::request", "data" : { "user" : OC.currentUser, "session_id" : Chat.sessionId, "timestamp" : (new Date).getTime() / 1000 }}, success);
            },
            join : function(convId, success){
                Chat.api.util.doRequest({"type" : "command::join::request", "data" : { "conv_id": convId,  "timestamp" : (new Date).getTime() / 1000, "user" : OC.currentUser, "session_id" : Chat.sessionId }}, success);
            },
            invite : function(userToInvite, convId, success, error){
                Chat.api.util.doRequest({"type" : "command::invite::request", "data" : { "conv_id" : convId, "timestamp" : (new Date).getTime() / 1000, "user_to_invite" : userToInvite , "user" : OC.currentUser, "session_id" : Chat.sessionId }},success,error);
            },
            sendChatMsg : function(msg, convId, success){
                Chat.api.util.doRequest({"type" : "command::send_chat_msg::request", "data" : {"conv_id" : convId, "chat_msg" : msg, "user" : OC.currentUser, "session_id" : Chat.sessionId, "timestamp" : (new Date).getTime() / 1000  }}, success);
            },
            leave : function(convId, success){
                Chat.api.util.doRequest({"type" : "command::leave::request", "data" : { "conv_id" : convId, "user" : OC.currentUser, "session_id" : Chat.sessionId, "timestamp" : (new Date).getTime() / 1000  }}, success);
            },
            online : function(success){
            	Chat.api.util.doRequest({"type" : "command::online::request", "data" : { "user" : OC.currentUser, "session_id" : Chat.sessionId, "timestamp" : (new Date).getTime() / 1000}}, function(){});
            }
    	},
    	on : {
            invite : function(data){
                Chat.scope.$apply(function(){
                    Chat.scope.addConvToView(data.conv_id, data.user);
                }); 
                Chat.api.command.join(data.conv_id, function(){});
                Chat.ui.alert('You auto started a new conversation with ' + data.user);
                Chat.ui.applyAvatar(data.user);
            },
            chatMessage : function(data){
                Chat.scope.$apply(function(){
                    Chat.scope.addChatMsgToView(data.conv_id, data.user, data.chat_msg, data.timestamp);	
                });
            },
            joined : function(data){
            	Chat.ui.alert('The user ' + data.user + ' joined this conversation');
            	Chat.scope.$apply(function(){
            		Chat.scope.convs[data.conv_id].users.push(data.user);	
                });
                Chat.ui.applyAvatar(data.user);
            }
    	},
    	util : {
            doRequest : function(request, success, error){
                $.post(OC.Router.generate("chat_api"), {JSON: JSON.stringify(request)}).done(function(response){
                	if(response.data.status === "success"){
                        success();
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
                        Chat.api.util.handlePushMessage(push_msg);
                    });
                    Chat.api.util.deletePushMessages(ids_del, function(){
                        Chat.api.util.longPoll();
                    });
                });
            },
            handlePushMessage : function(push_msg){
        		if (push_msg.type === "invite"){
        			Chat.api.on.invite(push_msg.data);
                } else if (push_msg.type === "send_chat_msg"){
                    Chat.api.on.chatMessage(push_msg.data);
                } else if (push_msg.type === "joined"){
                    Chat.api.on.joined(push_msg.data);
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
                $.post(OC.Router.generate('chat_api'), {"JSON" : JSON.stringify({"type" : "push::get::request", "data" : { "user" : OC.currentUser, "session_id" : Chat.sessionId}})}, function(data){
                    success(data);
                });
            },
            deletePushMessages : function(ids, success){
                $.post(OC.Router.generate('chat_api'), {"JSON" : JSON.stringify({"type" : "push::delete::request", "data" : {"user" : OC.currentUser, "session_id" : Chat.sessionId, ids: ids}})}, function(data){
                   success();
                });
            },
    	}
    },
}
