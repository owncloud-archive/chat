function throwSuccess(msg){
	$('#status').append('<li class="status-success">' +  msg +'</li>')
}

function throwError(msg){
	$('#status').append('<li class="status-error">' + msg + '</li>');
}
$(document).ready(function(){
	$.websocket('ws://'+window.location.hostname+':8080', function(server){
	console.log('Connection Established'); 

	// When the users log in, we have to greet the server
	greet(server, function(msg){
		if(msg.status === "success"){
			throwSuccess('Connected to the server');

			server.message = function(msg){
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
			}
			// We greeted the server, the real game starts here :)
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
				console.log($(this).data('conversationid'))
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

		} else {
			throwError(msg.data.msg);
		}
	});
	});
});

function initConversation(server){
	if($('#user').val() !== ''){
		if($('#user').val() === OC.currentUser){
			throwError('You can\'t stat a conversation with yourself');
			$('#user').val('');
		} else {
			$('#error').text('');
			var userToInvite = $('#user').val();
			$('#user').val('');
			var timestamp = "conversation" + (new Date).getTime()
			var conversationID = md5(timestamp);
			conversationID = conversationID.toString();

			join(server, conversationID, function(msg){
			console.log('joined');
			if (msg.status === "success"){
				// Conversation created and joined
				invite(server, userToInvite, conversationID,  function(msg){
					// User invited
					// Chat can div can be created and displayed
					// TODO use octemplate here to make this more readable
					var chat_template = '<section id="*CONVERSATIONID*"class="chatContainer"><h3>*USER*</h3><div class="chatLeft"><div class="chatText" id="chatText*CONVERSATIONID*"></div><input class="messagefield" data-CONVERSATIONID="*CONVERSATIONID*" type="text"  class="message"><footer><input class="invitefield" data-conversationID="*CONVERSATIONID*" type="text" ><button class="leave" data-CONVERSATIONID="*CONVERSATIONID*" title="Leave this conversation">Leave</button><button class="hide" data-conversationID="*CONVERSATIONID*" title="Hide this window">Hide</button></footer> ';
					var chat = chat_template.replace('*USER*', userToInvite).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID);
					$('#chats').append(chat);
					$('#conversations').append('<li id="conversation' + conversationID +'" data-displayed="true" data-conversationID="' + conversationID + '" data-user="' + userToInvite + '" class="conversation">'  + userToInvite + '</li>');


				});
			}
			});
		}



	} else {
		throwError('Username can\'t be empty')
	}
}

function joinConversation(server, conversationID, conversationName){
	join(server, conversationID, function(msg){
	console.log('joined');
	if (msg.status === "success"){
		// TODO use octemplate here to make this more readable
		var chat_template = '<section id="*CONVERSATIONID*"class="chatContainer"><h3>*USER*</h3><div class="chatLeft"><div class="chatText" id="chatText*CONVERSATIONID*"></div><input class="messagefield" data-CONVERSATIONID="*CONVERSATIONID*" type="text"  class="message"><footer><input class="invitefield" data-conversationID="*CONVERSATIONID*" type="text" ><button class="leave" data-CONVERSATIONID="*CONVERSATIONID*" title="Leave this conversation">Leave</button><button class="hide" data-conversationID="*CONVERSATIONID*" title="Hide this window">Hide</button></footer> ';
		var chat = chat_template.replace('*USER*', conversationName).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID);
		$('#chats').append(chat);
		$('#conversations').append('<li id="conversation' + conversationID +'" data-displayed="true" data-conversationID="' + conversationID + '" data-user="' + conversationName + '" class="conversation">'  + conversationName + '</li>');
	}
	});
}

function sendChatMessage(server, message, conversationID, callback){
	server.sendMSG(server.generateJSONcommand('send',{conversationID : conversationID, msg : message, user: OC.currentUser}), function(msg){
		console.log(' sendchatmessage callbak' + msg);
		console.log(msg);
		if (msg.status === "success"){
			callback(msg);
		} else if (msg.status === "error") {
			throwError('Can\'t send message: ' + message );        
		} else {
			onChatMessage({conversationID : conversationID, msg : message, user: OC.currentUser}); // Add message to the chat window
		}
	});
}

function deleteConversation(conversationID){
	$('#' + conversationID).remove();
	$('#conversation' + conversationID).remove();
}
function hideconversation(conversationID){
	$('#' + conversationID).fadeOut();
	$('#conversation' + conversationID).data('displayed', 'false');
}

function getUsers(server, conversationID, callback){
	server.sendMSG(server.generateJSONcommand('getusers', {conversationID : conversationID}), function(msg){
		if(msg.status === "success"){
			callback(msg);
		} else if (msg.status === "error"){
			throwError('Can\'t get user list because ' + msg.data.msg); 
		}
	});
}

function greet(server, callback){
	server.sendMSG(server.generateJSONcommand('greet', {user : OC.currentUser}), callback);
}

function join(server, conversationID, callback){
	server.sendMSG(server.generateJSONcommand('join', {user : OC.currentUser, conversationID : conversationID,  timestamp : (new Date).getTime()  }), callback);
}

function invite(server, userToInvite, conversationID, callback){
	server.sendMSG(server.generateJSONcommand('invite', {user : OC.currentUser, conversationID : conversationID, timestamp : (new Date).getTime(), userToInvite : userToInvite}), function(msg){
		if (msg.status === "success"){
			callback(msg);
		} else if(msg.status === "error"){
			throwError('Can\'t ivnite user: ' + userToInvite + 'because : ' + msg.data.msg);
		}
	});
}

function leave(server, conversationID, user, callback){
	server.sendMSG(server.generateJSONcommand('leave', {user: OC.currentUser, conversationID : conversationID}), function(msg){
		if (msg.status === "success"){
			callback(msg);
		} else {
			throwError('Can\'t leave room because ' + msg.data.msg);
		}
	});
}

function onInvite(server ,param){
	joinConversation(server, param.conversationID, param.user);
}

function onChatMessage(param){
	$('#chatText' + param.conversationID).append("<div class='chatmsg'>"+param.user+": "+param.msg+"</div>");
}
