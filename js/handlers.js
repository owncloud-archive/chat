$(function() {
    Chat.scope = angular.element($("#app")).scope();
    $(window).bind('beforeunload', function() {
        Chat.scope.$apply(function(){
            Chat.scope.quit();            
        });
    });
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
    //setInterval(Chat.util.titleHandler, 2000);
});
