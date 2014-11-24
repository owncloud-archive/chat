angular.module('chat').directive('autoCenter', [function () {
	function center(element){
		var windowHeight = $(window).height();
		var windowWidth = $(window).width();
		var elementHeight = element.height();
		var elementWidth = element.width();
		var left = (windowWidth - elementWidth) / 2;
		var top = (windowHeight - elementHeight) / 2;
		element.css('left', left);
		element.css('top', top);
	}
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			center(element);
			$(window).resize(function(){
				console.log('resize');
				center(element);
			});
		}
	};
}]);