function init(){
	Chat.ui.showLoading();
	Chat.sessionId = Chat.util.generateSessionId();
	Chat.api.command.greet(function(){
		Chat.ui.clear();
		Chat.ui.showEmpty();
	});
}
$( document ).ready(function() {
	OC.Router.registerLoadedCallback(function(){
		init();
	});
});