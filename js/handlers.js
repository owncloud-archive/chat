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
	$("#chat-msg-input-field").autosize({
		callback : function(){
			var height = $("#chat-msg-input-field").height();
			height = height + 36;
			$('#chat-window-footer').height(height);
			$('#chat-window-body').css('bottom', height);
			Chat.app.ui.scrollDown();
		}
	});
});
