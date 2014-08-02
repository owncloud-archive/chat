/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
$(function() {
    $(window).unload(function(){
        Chat.scope.$apply(function(){
            Chat.scope.quit();            
        });
    });
	$("#chat-msg-input-field").autosize({
		callback : function(){
			var height = $("#chat-msg-input-field").height();
			height = height + 36;
			$('#chat-window-footer').height(height);
			$('#chat-window-body').css('bottom', height);
			$('#chat-window-msgs').scrollTop($('#chat-window-msgs')[0].scrollHeight);
		}
	});
});
