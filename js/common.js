function throwSuccess(msg){
	$('#status').append('<li class="status-success">' +  msg +'</li>');
}

function throwError(msg){
	$('#status').append('<li class="status-error">' + msg + '</li>');
}