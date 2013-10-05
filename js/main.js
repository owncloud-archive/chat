function throwSuccess(msg){
	$('#status').append('<li class="status-success">' +  msg +'</li>')
}

function throwError(msg){
	$('#status').append('<li class="status-error">' + msg + '</li>');
}
$(document).ready(function(){
	$.websocket('ws://192.168.0.184:8080', function(server){
	console.log('Connection Established'); 

	// When the users log in, we have to greet the server
	greet(server, function(msg){
		if(msg.status == "success"){
			throwSuccess('Connected to the server');

			server.message = function(msg){
				if (msg.data.type == "invite"){
					onInvite(server, msg.data.param);
				} else if (msg.data.type == "send"){
					onChatMessage(msg.data.param);                        
				} else if (msg.data.type == "left"){
					var conservationID = msg.data.param.conservationID;
					getUsers(server, conservationID, function(msg){
						if (msg.data.param.users.length <= 1){
							deleteConservation(conservationID);
						} 
					});
				}
			}
			// We greeted the server, the real game starts here :)
			$('#createConverstation').click(function(){
				initConservation(server);   
			});

			$("body").on("click", ".conversation", function(event){
				$('#' + $(this).data('conservationid')).fadeIn();
			});

			$("body").on("keypress", ".messagefield", function(e){
				if(e.which == 13) {
					sendChatMessage(server, $(this).val(), $(this).data('conservationid'), function(msg){
					});
					$(this).val('');
				}
			});

			$('body').on("click", ".hide", function(){
				hideConservation($(this).data('conservationid')); 
			});

			$('body').on('click', '.leave', function(){
				console.log($(this).data('conservationid'))
				var conservationID = $(this).data('conservationid');
				leave(server, conservationID, OC.currentUser, function(msg){
					$('#' + conservationID).remove();
					$('#conservation' + conservationID).remove();
				});
			});

			$('#user').keypress(function(e){
				if (e.which == 13){
					initConservation(server)
				}
			});

			$('body').on('keypress', '.invitefield', function(event){
				if(event.which == 13){
					invite(server, $(this).val(), $(this).data('conservationid'), function(msg){
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

function initConservation(server){
	if($('#user').val() !== ''){
		if($('#user').val() === OC.currentUser){
			throwError('You can\'t stat a conservation with yourself');
			$('#user').val('');
		} else {
			$('#error').text('');
			var userToInvite = $('#user').val();
			$('#user').val('');
			var timestamp = "conservation" + (new Date).getTime()
			var conservationID = CryptoJS.MD5(timestamp);
			conservationID = conservationID.toString();

			join(server, conservationID, function(msg){
			console.log('joined');
			if (msg.status === "success"){
				// Conservation created and joined
				invite(server, userToInvite, conservationID,  function(msg){
					// User invited
					// Chat can div can be created and displayed
					// TODO use octemplate here to make this more readable
					var chat_template = '<section id="*CONSERVATIONID*"class="chatContainer"><h3>*USER*</h3><div class="chatLeft"><div class="chatText" id="chatText*CONSERVATIONID*"></div><input class="messagefield" data-CONSERVATIONID="*CONSERVATIONID*" type="text"  class="message"><footer><input class="invitefield" data-conservationID="*CONSERVATIONID*" type="text" ><button class="leave" data-CONSERVATIONID="*CONSERVATIONID*" title="Leave this conversation">Leave</button><button class="hide" data-conservationID="*CONSERVATIONID*" title="Hide this window">Hide</button></footer> ';
					var chat = chat_template.replace('*USER*', userToInvite).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID);
					$('#chats').append(chat);
					$('#conversations').append('<li id="conservation' + conservationID +'" data-displayed="true" data-conservationID="' + conservationID + '" data-user="' + userToInvite + '" class="conversation">'  + userToInvite + '</li>');


				});
			}
			});
		}



	} else {
		throwError('Username can\'t be empty')
	}
}

function joinConservation(server, conservationID, conservationName){
	join(server, conservationID, function(msg){
	console.log('joined');
	if (msg.status == "success"){
		// TODO use octemplate here to make this more readable
		var chat_template = '<section id="*CONSERVATIONID*"class="chatContainer"><h3>*USER*</h3><div class="chatLeft"><div class="chatText" id="chatText*CONSERVATIONID*"></div><input class="messagefield" data-CONSERVATIONID="*CONSERVATIONID*" type="text"  class="message"><footer><input class="invitefield" data-conservationID="*CONSERVATIONID*" type="text" ><button class="leave" data-CONSERVATIONID="*CONSERVATIONID*" title="Leave this conversation">Leave</button><button class="hide" data-conservationID="*CONSERVATIONID*" title="Hide this window">Hide</button></footer> ';
		var chat = chat_template.replace('*USER*', conservationName).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID).replace('*CONSERVATIONID*', conservationID);
		$('#chats').append(chat);
		$('#conversations').append('<li id="conservation' + conservationID +'" data-displayed="true" data-conservationID="' + conservationID + '" data-user="' + conservationName + '" class="conversation">'  + conservationName + '</li>');
	}
	});
}

function sendChatMessage(server, message, conservationID, callback){
	server.sendMSG(server.generateJSONcommand('send',{conservationID : conservationID, msg : message, user: OC.currentUser}), function(msg){
		console.log(' sendchatmessage callbak' + msg);
		console.log(msg);
		if (msg.status == "success"){
			callback(msg);
		} else if (msg.status == "error") {
			throwError('Can\'t send message: ' + message );        
		} else {
			onChatMessage({conservationID : conservationID, msg : message, user: OC.currentUser}); // Add message to the chat window
		}
	});
}

function deleteConservation(conservationID){
	$('#' + conservationID).remove();
	$('#conservation' + conservationID).remove();
}
function hideConservation(conservationID){
	$('#' + conservationID).fadeOut();
	$('#conservation' + conservationID).data('displayed', 'false');
}

function getUsers(server, conservationID, callback){
	server.sendMSG(server.generateJSONcommand('getusers', {conservationID : conservationID}), function(msg){
		if(msg.status == "success"){
			callback(msg);
		} else if (msg.status == "error"){
			throwError('Can\'t get user list because ' + msg.data.msg); 
		}
	});
}

function greet(server, callback){
	server.sendMSG(server.generateJSONcommand('greet', {user : OC.currentUser}), callback);
}

function join(server, conservationID, callback){
	server.sendMSG(server.generateJSONcommand('join', {user : OC.currentUser, conservationID : conservationID,  timestamp : (new Date).getTime()  }), callback);
}

function invite(server, userToInvite, conservationID, callback){
	server.sendMSG(server.generateJSONcommand('invite', {user : OC.currentUser, conservationID : conservationID, timestamp : (new Date).getTime(), userToInvite : userToInvite}), function(msg){
		if (msg.status == "success"){
			callback(msg);
		} else if(msg.status == "error"){
			throwError('Can\'t ivnite user: ' + userToInvite + 'because : ' + msg.data.msg);
		}
	});
}

function leave(server, conservationID, user, callback){
	server.sendMSG(server.generateJSONcommand('leave', {user: OC.currentUser, conservationID : conservationID}), function(msg){
		if (msg.status == "success"){
			callback(msg);
		} else {
			throwError('Can\'t leave room because ' + msg.data.msg);
		}
	});
}

function onInvite(server ,param){
	joinConservation(server, param.conservationID, param.user);
}

function onChatMessage(param){
	$('#chatText' + param.conservationID).append(param.msg);
}
