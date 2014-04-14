(function ($) {
	$.fn.online = function(isonline) {
		var $div = this;

		console.log(isonline);
		if(isonline === 'true' || isonline === true){
			$div.addClass('online');
			$div.removeClass('offline');
		} else {
			$div.removeClass('online');
			$div.addClass('offline');
		}
	};
}(jQuery));
