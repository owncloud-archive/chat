$(function() {
    Chat.scope = angular.element($("#app")).scope();
    $(window).unload(function(){
        Chat.scope.$apply(function(){
            Chat.scope.quit();            
        });
    });
    Chat.scope.$apply(function(){
        Chat.scope.init();            
    });
});
