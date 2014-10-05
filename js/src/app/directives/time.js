angular.module('chat').directive('time', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			var time = moment.unix(parseInt(attrs.timestamp)).format('h:mm');
			element.text(time);
		}
	};
});