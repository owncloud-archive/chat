alert('ajax-client');
$(document).ready(function(){
	greet(function(msg){
		throwSuccess('Connected to the server');

		/*
		 * TODO: Create long polling fucntion which checks for push messages
		 *
			if (msg.data.type === "invite"){
				onInvite(server, msg.data.param);
			} else if (msg.data.type === "send"){
				onChatMessage(msg.data.param);                        
			} else if (msg.data.type === "left"){
				var conversationID = msg.data.param.conversationID;
				getUsers(server, conversationID, function(msg){
					if (msg.data.param.users.length <= 1){
						deleteConversation(conversationID);
					} 
				});
			}
		*/	
		
		$('#createConverstation').click(function(){
			initConversation(server);   
		});

		$("body").on("click", ".conversation", function(event){
			$('#' + $(this).data('conversationid')).fadeIn();
		});

		$("body").on("keypress", ".messagefield", function(e){
			if(e.which === 13) {
				sendChatMessage(server, $(this).val(), $(this).data('conversationid'), function(msg){
				});
				$(this).val('');
			}
		});

		$('body').on("click", ".hide", function(){
			hideConversation($(this).data('conversationid')); 
		});

		$('body').on('click', '.leave', function(){
			var conversationID = $(this).data('conversationid');
			leave(server, conversationID, OC.currentUser, function(msg){
				$('#' + conversationID).remove();
				$('#conversation' + conversationID).remove();
			});
		});

		$('#user').keypress(function(e){
			if (e.which === 13){
				initConversation(server)
			}
		});

		$('body').on('keypress', '.invitefield', function(event){
			if(event.which === 13){
				invite(server, $(this).val(), $(this).data('conversationid'), function(msg){
				});
				$(this).val('');
			} 
		});
	});
});

function greet(success){
	sendMSG('greet', {user: OC.currentUser}, success, function(errorMsg){
   		throwError(errorMsg);
	});
}

function invite(userToInvite, conversationID, success){
	
	sendMSG('invite', {user : OC.currentUser, conversationid : conversationID, timestamp : (new Date).getTime(), usertoinvite : userToInvite},success, function(errorMsg){
		throwError(errorMsg);
	});
	
}

