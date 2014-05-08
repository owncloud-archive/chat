Chat.och.on = {
    newConv : function(userToInvite, success){
    	if(!Array.isArray(userToInvite)){
    		userToInvite = [userToInvite];
    	}
        Chat.och.api.command.startConv(
            userToInvite,
            function(response){
                success(response.data.conv_id, userToInvite);
            },
            function(errorMsg){
                if(errorMsg === 'USER-TO-INVITE-NOT-ONLINE'){
                    Chat.app.view.alert('The user you tried to invite isn\'t online, you already can send messages');// TODO
                } else if(errorMsg === 'USER-TO-INVITE-NOT-OC-USER'){
                    Chat.app.view.alert('The user you tried to invite isn\'t a valid owncloud user')
                    // Leave the already joined conversation
                    Chat.och.api.command.leave(newConvId, function(){});
                } else {
                    Chat.app.view.alert(errorMsg);
                }
            }
        );
    },
    sendChatMsg : function(convId, msg){
        Chat.och.api.command.sendChatMsg(msg, convId, function(){});
    },
    invite : function(convId, userToInvite){
    	var users = [];
    	angular.forEach(Chat.scope.convs[convId].users, function(user){
    		users.push(user);
    	});
    	users.push(userToInvite);
    	Chat.och.on.newConv(users, function(convId, users){
			Chat.app.view.addConv(convId, users, Chat.scope.backends.och);
    	});
    	
    },
    leave : function(convId, success){
    	Chat.och.api.command.deleteInitConv(convId, function(){});
    	success();
    },
        applyAvatar : function(element, user, size){
        element.avatar(user, size);
    }
};