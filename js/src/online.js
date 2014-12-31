/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
(function ($) {
	$.fn.online = function(isonline) {
		var $div = this;
		if(isonline === 'true' || isonline === true){
			$div.addClass('online-dot');
		} else {
			$div.removeClass('online-dot');
		}
	};
}(jQuery));
