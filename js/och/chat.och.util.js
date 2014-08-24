/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
Chat.och.util = {
	init : function(){
		$(function(){
			Chat.och.sessionId = Chat.scope.initvar.sessionId;
			Chat.och.api.util.longPoll();
			// Now join and add all the existing convs
			for ( var key in Chat.scope.initConvs.och) {
				var conv = Chat.scope.initConvs.och[key];
				var contacts = [];
				for (var key in conv.users ){
					var user = conv.users[key];
					contacts.push(Chat.scope.contactsObj[user]);
				}
				Chat.app.view.addConv(conv.id, contacts, Chat.scope.backends.och, []);
				conv.messages.forEach(function(msg){
					Chat.app.view.addChatMsg(conv.id, Chat.scope.contactsObj[msg.user], msg.msg, msg.timestamp, Chat.scope.backends.och, true);
				});
			}
			setInterval(Chat.och.util.updateOnlineStatus, 60000);
		});
	},
	quit : function(){
		Chat.och.api.command.offline();
//		for(var key in Chat.scope.convs) {
//			var conv = Chat.scope.convs[key];
//			if(conv.backend.name === 'och'){
//				Cache.set(conv.id, {msgs: conv.raw_msgs});
//			}
//		}/*/
	},
	updateOnlineStatus : function(){
		Chat.och.api.command.online();
	}
};
