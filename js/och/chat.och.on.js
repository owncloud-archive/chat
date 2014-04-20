Chat.och.on = {
    newConv : function(userToInvite, success){
        Chat.och.api.command.startConv(
            userToInvite,
            function(response){
                success(response.data.conv_id, [userToInvite]);
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
        console.log('Chat.och.on.invite called');
        Chat.och.api.command.invite(
            userToInvite,
            convId,
            function(){ // Success
            
            },
            function(errorMsg){ // Error
                if(errorMsg === 'USER-TO-INVITE-NOT-ONLINE'){
                    Chat.app.view.alert('The user you tried to invite isn\'t online, you already can send messages');// TODO
                } else if(errorMsg === 'USER-TO-INVITE-NOT-OC-USER'){
                    Chat.app.view.alert('The user you tried to invite isn\'t a valid owncloud user')
                }
            }
     );
    },
    leave : function(convId, success){
    	console.log('deleting ' + convId + ' from initconvs');
    	Chat.och.api.command.deleteInitConv(convId, function(){});
    	success();
    },
        applyAvatar : function(element, user, size){
        element.avatar(user, size);
    }
};