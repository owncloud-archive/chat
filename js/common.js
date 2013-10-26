function throwSuccess(msg){
	$('#status').append('<li class="status-success">' +  msg +'</li>');
}

function throwError(msg){
	$('#status').append('<li class="status-error">' + msg + '</li>');
}

function deleteConversation(conversationID){
	$('#' + conversationID).remove();
	$('#conversation' + conversationID).remove();
}

function hideconversation(conversationID){
	$('#' + conversationID).fadeOut();
	$('#conversation' + conversationID).data('displayed', 'false');
}