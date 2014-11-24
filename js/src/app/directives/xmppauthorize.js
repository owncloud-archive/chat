angular.module('chat').directive('xmppAuthorize', ['authorize', function(authorize) {
	return {
		restrict: 'E',
		link: function ($scope, element, attrs) {
			$scope.authorize = authorize;
		}
	};
}]);