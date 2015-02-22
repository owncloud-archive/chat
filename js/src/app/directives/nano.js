angular.module('chat').directive('nano', ['$timeout',function($timeout) {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			$scope.$on('scrollBottom', function () {
				setTimeout(function () { // You might need this timeout to be sure its run after DOM render.
					jQuery("#chat-wrapper").scrollTop(jQuery("#chat-wrapper")[0].scrollHeight);
				}, 1000);
				setTimeout(function () { // You might need this timeout to be sure its run after DOM render.
					jQuery("#chat-wrapper").scrollTop(jQuery("#chat-wrapper")[0].scrollHeight);
				}, 1000);
			});
			$scope.$on('scrollTop', function () {
				// FIXME
			});
		}
	};
}]);