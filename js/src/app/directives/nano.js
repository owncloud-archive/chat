angular.module('chat').directive('nano', ['$timeout',function($timeout) {
	$(".nano").nanoScroller();
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			$scope.$on('scrollBottom', function () {
				setTimeout(function () { // You might need this timeout to be sure its run after DOM render.
					$(".nano").nanoScroller({ scroll: 'bottom' });
				}, 1000);
			});
			$scope.$on('scrollTop', function () {
				setTimeout(function () { // You might need this timeout to be sure its run after DOM render.
					$(".nano").nanoScroller({ scroll: 'bottom' });
				}, 1000);
			});
		}
	};
}]);