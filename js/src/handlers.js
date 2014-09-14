/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
$(window).unload(function(){
	Chat.scope.$apply(function(){
		Chat.scope.quit();
	});
});

