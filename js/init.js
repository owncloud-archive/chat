function init(){
	Chat.ui.showLoading();
	Chat.sessionId = Chat.util.generateSessionId();
	Chat.api.command.greet(function(){
		//TODO add getConversation function
		Chat.ui.clear();
		Chat.ui.showEmpty();
		Chat.api.util.longPoll();
	});
}
$( document ).ready(function() {
	OC.Router.registerLoadedCallback(function(){
		init();
	});
});