angular.module('chat').factory('activeConv', ['scope', function(scope){
	return scope.active.conv;
}]);