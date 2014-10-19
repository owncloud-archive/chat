angular.module('chat').factory('backends', ['initvar', '$injector', function(initvar, $injector) {
	var backends = initvar.backends;
	var result = {};
	for (var id in backends){
		result[id] = backends[id];
		result[id].handle = $injector.get(id);
	}
	return result;
}]);