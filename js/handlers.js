$(function() {
    $(window).bind('beforeunload', function() {
        Chat.util.quit();
    });
	OC.Router.registerLoadedCallback(function(){
		Chat.util.init();
	});
	$(window).on("blur focus", function(e) {
	    var prevType = $(this).data("prevType");

	    if (prevType != e.type) {   
	        switch (e.type) {
	            case "blur":
	            	Chat.tabActive = false;
	            	break;
	            case "focus":
	            	Chat.tabTitle = 'Chat - ownCloud';
	            	Chat.tabActive = true;
	            	break;
	        }
	    }
	    $(this).data("prevType", e.type);
	});
	setInterval(Chat.util.titleHandler, 2000);
	setInterval(Chat.util.updateOnlineStatus, 60000);
});
