(function ($) {
	$.fn.online = function(isonline, size) {
		var $div = this;

		console.log(isonline);
		if(isonline === 'true' || isonline === true){
			$div.parent().css('border-left', size + 'px solid #3CB371');
		} else {
			$div.parent().css('border-left', size + 'px solid #B22222');
		}
	};
}(jQuery));
