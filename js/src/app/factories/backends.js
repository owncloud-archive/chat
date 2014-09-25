angular.module('chat').factory('backends', ['initvar', 'och', function(initvar, och) {
	var backends = initvar.backends;
	backends.och.handle = och;
	return backends;
}]);