Chat.och.on = {
	newConv : function(userToInvite, success){
		Chat.och.api.command.startConv(
			userToInvite,
			function(response){
				// check if we are already in the conv
				if(Chat.scope.convs[response.data.conv_id] === undefined){
					console.log(response);
					success(response.data.conv_id, response.data.users, response.data.messages);
				} else {
					// we are already in the conv -> make it active
					Chat.app.view.makeActive(response.data.conv_id);
				}
			},
			function(errorMsg){
				if(errorMsg === 'USER-TO-INVITE-NOT-ONLINE'){
					Chat.app.view.alert('The user you tried to invite isn\'t online, you already can send messages');// TODO
				} else if(errorMsg === 'USER-TO-INVITE-NOT-OC-USER'){
					Chat.app.view.alert('The user you tried to invite isn\'t a valid owncloud user');
					// Leave the already joined conversation
					Chat.och.api.command.leave(newConvId, function(){});
				} else if (errorMsg === 'SESSION-ALREADY-JOINED') {
					Chat.app.view.alert(errorMsg);
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
	archive : function(convId){
		Chat.och.api.command.archive(convId);
	},
	unArchive : function(convId){
		Chat.och.api.command.unArchive(convId);
	},
	applyAvatar : function(element, user, size){
		element.avatar(user, size);
	}
};