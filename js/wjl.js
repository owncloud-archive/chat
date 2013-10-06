(function($){
$.extend({
	websocket: function(url, callback) {
		var server = new WebSocket(url);
		server.messages = [];

		server.sendMSG = function(msg, callback){ 
			msgJSON =JSON.stringify(msg);
			this.send(msgJSON);
			var command = new Object();            
			command.id = msg.id;
			command.callback = callback;
			server.messages.push(command);
		}

		server.onmessage = function(e){
			var msg = JSON.parse(e.data);
			var found = false;
			$.each(server.messages, function(key, val){
				if(val.id === msg.id){
					val.callback(msg);
					found = true;
				} 
			});
			if (found === false){
				server.message(msg);
			}
			found = false;

		}

		server.generateJSONsucces = function(id){
			var returnObject = new Object();
			returnObject.status = "success";
			returnObject.id = id;
			return returnObject;
		}

		server.generateJSONerror = function(id, msg){
			var returnObject = new Object();
			returnObject.status = "error";
			returnObject.id = id;
			returnObject.data = new Object();
			returnObject.data.msg = msg;
			return returnObject;
		}

		server.generateJSONcommand = function(type, param){
			var returnObject = new Object();
			returnObject.status = "command";
			returnObject.id = server.generateId();
			returnObject.data = new Object();
			returnObject.data.type = type;
			returnObject.data.param = param;
			return returnObject;
		}

		server.generateId  = function(){
			timestamp = 'id' + (new Date).getTime() + Math.random();
			id = CryptoJS.MD5(timestamp);
			id = id.toString();
			return id;
		}

	if (typeof callback === 'function') { 
		server.onopen = function(e) {
			callback(server);
		};
	}

	$(window).unload(function(){ server.close(); server = null });

	}
});
})(jQuery);
