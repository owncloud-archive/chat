Chat.och.util = {
	init : function(){
		Chat.och.sessionId = Chat.scope.initvar.sessionId;
		Chat.och.api.util.longPoll();
		// Now join and add all the existing convs
		angular.forEach(Chat.scope.initConvs.och, function(conv){
			var contacts = [];
			angular.forEach(conv.users, function(user){
				contacts.push(Chat.scope.contactsObj[user]);
			});
			Chat.app.view.addConv(conv.id, contacts, Chat.scope.backends.och, [], conv.archived);
			Chat.och.api.command.join(conv.id, function(){});
//			var cachedMsgs = Cache.get(conv.id);
//			cachedMsgs = cachedMsgs.msgs;
//			if(cachedMsgs !== undefined){
//				for (var i = 0; i < cachedMsgs.length; i++) {
//					var msg = cachedMsgs[i];
//					Chat.app.view.addChatMsg(conv.id, msg.user, msg.msg, msg.timestamp, Chat.scope.backends.och, true);
//					if(i === cachedMsgs.length -1){
//						var timestampLatestMsg = msg.timestamp;
//					}
//				}
//			} else {
//				var timestampLatestMsg = 0;
//			}
//			Chat.och.api.command.getMessages(conv.id, timestampLatestMsg, function(data){
			Chat.och.api.command.getMessages(conv.id, 0, function(data){
				data.data.messages.forEach(function(msg){
					Chat.app.view.addChatMsg(conv.id, Chat.scope.contactsObj[msg.user], msg.msg, msg.timestamp, Chat.scope.backends.och, true);
				});
			})

		});
		setInterval(Chat.och.util.updateOnlineStatus, 60000);
	},
	quit : function(){
		Chat.och.api.command.offline();
		angular.forEach(Chat.scope.convs, function(conv){
			if(conv.backend.name === 'och'){
				Cache.set(conv.id, {msgs: conv.raw_msgs});
			}
		});

	},
	updateOnlineStatus : function(){
		Chat.och.api.command.online();
	}
};
