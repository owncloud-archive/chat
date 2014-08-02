/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
(function ($) {
	$.fn.online = function(isonline, size) {
		var $div = this;
		if(isonline === 'true' || isonline === true){
			$div.parent().css('border-left', size + 'px solid #3CB371');
		} else {
			$div.parent().css('border-left', size + 'px solid #B22222');
		}
	};
}(jQuery));
