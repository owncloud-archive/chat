angular.module('chat').filter('emoji', function() {
	return function(input) {
		console.log(input);
		return emojione.toImage(input);
	};
});
