/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
Chat.och.on = {
	newConv : function(userToInvite, success){
		Chat.och.api.command.startConv(
			userToInvite,
			function(response){
				// check if we are already in the conv
				if(Chat.scope.convs[response.data.conv_id] === undefined){
					success(response.data.conv_id, response.data.users, response.data.messages);
				} else {
					// we are already in the conv -> make it active
					Chat.app.view.makeActive(response.data.conv_id);
				}
			}
		);
	},
	sendChatMsg : function(convId, msg){
		Chat.och.api.command.sendChatMsg(msg, convId, function(){});
	},
	invite : function(convId, userToInvite){
		if(Chat.scope.convs[convId].users.length > 2){
			// We are in a group conversation
			Chat.och.api.command.invite(userToInvite, convId, function(data){
				var users = data.data.users;
				Chat.app.view.replaceUsers(convId, users);
			});
		} else {
			var users = [];
			angular.forEach(Chat.scope.convs[convId].users, function(user){
				users.push(user);
			});
			users.push(userToInvite);
			Chat.och.on.newConv(users, function(convId, users, msgs){
				Chat.app.view.addConv(convId, users, Chat.scope.backends.och, msgs);
			});
		}
	},
};