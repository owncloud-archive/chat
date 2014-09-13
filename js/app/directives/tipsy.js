Chat.angular.directive('tipsy', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			element.tipsy();
		}
	};
})