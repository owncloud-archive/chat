angular.module('chat').factory('initvar', [function() {
	var initvar = JSON.parse($('#initvar').text());
	$('#initvar').text('');
	return initvar;
}]);