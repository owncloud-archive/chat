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
			angular.forEach(Chat.scope.initConvs.och, function(conv){
				var contacts = [];
				angular.forEach(conv.users, function(user){
					contacts.push(Chat.scope.contactsObj[user]);
				});
				Chat.app.view.addConv(conv.id, contacts, Chat.scope.backends.och, [], conv.archived);
				conv.messages.forEach(function(msg){
					Chat.app.view.addChatMsg(conv.id, Chat.scope.contactsObj[msg.user], msg.msg, msg.timestamp, Chat.scope.backends.och, true);
				});
			});
			setInterval(Chat.och.util.updateOnlineStatus, 60000);
		});
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
