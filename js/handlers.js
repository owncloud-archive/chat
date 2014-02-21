$(function() {
    $(window).bind('beforeunload', function() {
    // TODO    Chat.util.quit();
    });
    Chat.scope = angular.element($("#app")).scope();
    Chat.scope.$apply(function(){
        Chat.scope.init();            
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
// TODO setInterval(Chat.util.titleHandler, 2000);
// TODO	setInterval(Chat.util.updateOnlineStatus, 60000);
});
