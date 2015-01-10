angular.module('chat').directive('scroll', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			$scope.$watch('convs[$session.conv].msgs', function(){
					var msgs = document.getElementById("chat-window-msgs");
					if (msgs !== null) {
						msgs.scrollTop = msgs.scrollHeight;
					}
			}, true);

		}
	};
});