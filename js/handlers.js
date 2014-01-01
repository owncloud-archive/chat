$(function() {
    $(window).bind('beforeunload', function() {
        Chat.util.quit();
    });
	OC.Router.registerLoadedCallback(function(){
		Chat.util.init();
	});
});