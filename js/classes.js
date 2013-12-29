var Chat = {
	settings : {
	},
    ui : {
    	clear : function(){
    		$('.panel').hide();
    	},
    	markConvActive : function(convId){
    		$('.conv-list-item').removeClass('conv-list-active');
    		$('#conv-list-' + convId).addClass('conv-list-active');
		},
    	scrollDown : function(){
    		$('#chat-window-body').scrollTop($('#chat-window-body')[0].scrollHeight);
    	},
    	showLoading : function(){
    		$('#loading-panel').show();
    	}, 
    	showEmpty : function(){
    		$('#empty-panel').show();
    		this.focus('#empty-panel-new-conv');
    	},
    	showChat : function(){
    		$('#chat-panel').show();
    	},
    	hideHeader : function(){
    		$('#header').hide();
    		$('#content-wrapper').css('padding-top', 0);
    	},
    	showHeader : function(){
    		$('#header').show();
    		$('#content-wrapper').css('padding-top', '3.5em');
    	},
    	focus : function(element){
    		$(element).focus();
    	},
    	focusMsgInput : function(){
    		if(!Chat.util.checkMobile()) {
    			this.focus('#chat-msg-input');
    		}
    	},
    	getFirstConv : function(){
    		id = $("#app-navigation li:nth-child(2)").attr('id'); // conv-id-...
    		convId = id.substr(10, id.length); // strip conv-id-
    		return convId;
    	},
    	newMsg : function(convId){
    		$('#conv-new-msg-' + convId).fadeIn();
    		$('#conv-new-msg-' + convId).fadeOut();
    		this.newMsg(convId);
    	},
    },
    util : {
    	generateSessionId : function(){
			var timestamp = "sessionID" + (new Date).getTime();
			var sessionID = md5(timestamp);
			return sessionID.toString();
    	},
    	generateConvId : function(){
			var timestamp = "conversation" + (new Date).getTime();
		    var conversationID = md5(timestamp);
		    return conversationID.toString();
    	},
    	timeStampToDate : function(timestamp){
	    	var date = new Date(timestamp*1000);
	    	var hours = date.getHours();
	    	var minutes = date.getMinutes();
	    	var seconds = date.getSeconds();
	    	return	{hours : date.getHours(), minutes : date.getMinutes(), seconds : date.getSeconds()};
    	},
    	countObjects : function(object){
    		var count = 0;
    		for (var k in object) {
    		    if (object.hasOwnProperty(k)) {
    		       ++count;
    		    }
    		}
    		return count;
    	},
    	checkMobile : function(){
    		return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    	},
    	throwError : function(msg){
    		alert(msg);
    	}

    },
    api : {
    	command : {
    		greet : function(success){
    			Chat.api.util.doRequest('greet', {"user" : OC.currentUser, "sessionID" : Chat.sessionId }, success);
    		}
    	},
    	util : {
    		doRequest : function(type, param, success){
    			var route = OC.Router.generate("command_" + type);
    			$.post(route, param).done(function(data){
    				console.log(data);
    	            if(data.status === "success"){
    	                    success();
    	            } else if (data.status === "error"){
    	                    Chat.util.throwError(data.data.msg);
    	            }
    	        });
    		}
    	}
    },
    // The following class is only used for testing, in the real app it will be deleted
    testing : {
    	simulateChatMessage : function(convId){
    		var scope = angular.element($("#chat-wrapper")).scope();
    		Chat.testing.addChatMessage({
    			msg : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed viverra, purus vel ultrices auctor, lacus orci interdum lacus, ac blandit.',
    			user : 'Derpina',
    			timestamp : new Date().getTime() / 1000,
    			conversationID : scope.activeConv 
    		});
    		Chat.testing.addChatMessage({
    			msg : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed viverra, purus vel ultrices auctor, lacus orci interdum lacus, ac blandit.',
    			user : 'Derpina',
    			timestamp : new Date().getTime() / 1000,
    			conversationID : scope.activeConv 
    		});	Chat.testing.addChatMessage({
    			msg : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed viverra, purus vel ultrices auctor, lacus orci interdum lacus, ac blandit.',
    			user : 'Derpina',
    			timestamp : new Date().getTime() / 1000,
    			conversationID : scope.activeConv 
    		});	Chat.testing.addChatMessage({
    			msg : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed viverra, purus vel ultrices auctor, lacus orci interdum lacus, ac blandit.',
    			user : 'Derpina',
    			timestamp : new Date().getTime() / 1000,
    			conversationID : scope.activeConv 
    		});	Chat.testing.addChatMessage({
    			msg : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed viverra, purus vel ultrices auctor, lacus orci interdum lacus, ac blandit.',
    			user : 'Derpina',
    			timestamp : new Date().getTime() / 1000,
    			conversationID : scope.activeConv 
    		});	Chat.testing.addChatMessage({
    			msg : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed viverra, purus vel ultrices auctor, lacus orci interdum lacus, ac blandit.',
    			user : 'Derpina',
    			timestamp : new Date().getTime() / 1000,
    			conversationID : scope.activeConv 
    		});
    	},
    	addChatMessage : function(msg){
    		msg.align = 'left';
    		msg.time = Chat.util.timeStampToDate(msg.timestamp); 
    	    var scope = angular.element($("#chat-wrapper")).scope();
    	    scope.$apply(function(){
    	        scope.convs[msg.conversationID].msgs.push(msg);
    	    });
    	    Chat.ui.scrollDown();
    	}
    }
}
