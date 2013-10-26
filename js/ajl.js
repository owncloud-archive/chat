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
