angular.module('chat').filter('emoji', function() {
	return function(input) {
		return emojione.toImage(input);
	};
});
