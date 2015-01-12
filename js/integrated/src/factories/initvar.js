angular.module('chat').factory('initvar', ['$http', function($http) {
	if (window.initVar === undefined){
		$.post('/index.php/apps/chat/initvar', function (data) {
			window.initVar = data;
		});
		return window.initVar;
	}
	return window.initVar;
}]);