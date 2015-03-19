angular.module('chat').directive('refresh', [function() {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			var lastScrollTop = 0;
			var last = Date.now() / 1000;
			element.scroll(function(event){
				var st = element.scrollTop();
				if (st < lastScrollTop){
					if (st < (element.height() - element.height() /4 )){
						if ((Date.now() / 1000) > last +1 ){
							$scope.loadOldMessages($scope.$session.conv);
							last = Date.now() / 1000;
						}
					}
				}
				lastScrollTop = st;
			});
		}
	};
}]);