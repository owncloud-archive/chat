angular.module('chat').factory('activeConv', ['scope', function(scope){
	return function() {
		if (scope.active !== undefined) {
			return scope.active.conv;
		} else {
			return null;
		}
	}
}]);