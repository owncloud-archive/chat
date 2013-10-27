/*
 * Ajax jQuery Layer
 */
function sendMSG(type, param, success, error){

	$.post(OC.Router.generate("command_" + type), param).done(function(data){
    	if(data.status === "success"){
    		success();
    	} else if (data.status === "error"){
    		error(data.data.msg);
    	}
	});
	
}

function getPushMessge(callback){
	$.post(OC.Router.generate('push_get'), {receiver: OC.currentUser}, function(data){
	   callback(data);
	});
}

function deletePushMessage(id, callback){
	$.post(OC.Router.generate('push_delete'), {id: id}, function(data){
	   callback();
	});
}
