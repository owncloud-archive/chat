angular.module('chat').factory('sessionId', ['initvar', function(initvar) {
	return initvar.sessionId;
}]);