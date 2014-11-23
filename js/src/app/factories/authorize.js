angular.module('chat').factory('authorize', [function() {
	return {
		show: false,
		name: null,
		jid: null,
		approve: null,
		deny: null
	}

}]);