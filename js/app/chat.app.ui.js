Chat.app.ui = {
	scrollDown : function(){
		$('#chat-window-msgs').scrollTop($('#chat-window-msgs')[0].scrollHeight);
	},
	focus : function(element){
		$(element).focus();
	},
	focusMsgInput : function(){
		if(!Chat.util.checkMobile()) {
			Chat.ui.focus('#chat-msg-input');
		}
	},
	getFirstConv : function(){
		id = $("#app-navigation li:nth-child(2)").attr('id'); // conv-id-...
		convId = id.substr(10, id.length); // strip conv-id-
		return convId;
	},
	getConvListIndex : function(convId){
	    return $("#conv-list-" + convId).index() + 1
	},
	newMsg : function(convId){
		$('#conv-new-msg-' + convId).fadeIn();
		$('#conv-new-msg-' + convId).fadeOut();
		this.newMsg(convId);
	},
	applyAvatar : function(user){
	    $('.icon-' + user).avatar(user, 32);
	},
	updateTitle : function(){
		if(!Chat.tabActive){
			$('title').text(Chat.tabTitle);
		}
	},
	clearTitle : function(){
		$('title').text('Chat - ownCloud');
	},
	alert : function(text){
		OC.Notification.showHtml(text);
		setTimeout(function(){
            OC.Notification.hide();
		}, 2000);
	},
};