Chat.och.util = {
	init : function(){
		Chat.och.sessionId = Chat.scope.initvar.sessionId;
		// Now join and add all the existing convs
		angular.forEach(Chat.scope.initConvs.och, function(conv){
			console.log('auto joining ' + conv.id);
			var contacts = [];
			angular.forEach(conv.users, function(user){
				contacts.push(Chat.scope.contactsObj[user]);
			});
			Chat.app.view.addConv(conv.id, contacts, Chat.scope.backends.och);
			Chat.och.api.command.join(conv.id, function(){});
			Chat.och.api.command.getMessages(conv.id, function(data){
				data.data.messages.forEach(function(msg){
					Chat.app.view.addChatMsg(conv.id, Chat.scope.contactsObj[msg.user], msg.msg, msg.timestamp, Chat.scope.backends.och);
				});
			})

		});
		setInterval(Chat.och.util.updateOnlineStatus, 60000);
	},
	quit : function(){
		Chat.och.api.command.offline();
	},
	updateOnlineStatus : function(){
		console.log('Updating online status add ' + (new Date).getTime() / 1000);
		Chat.och.api.command.online();
	},
};
