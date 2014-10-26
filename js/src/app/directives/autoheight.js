angular.module('chat').directive('autoHeight', [function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			if(attrs.convId === $scope.$session.conv){
				attrs.itemCount++;
			} else {
				element.css('height', attrs.minHeight + 'px');
			}
			var height = attrs.itemCount * attrs.itemHeight;
			if(height < attrs.minHeight){
				element.css('height', attrs.minHeight + 'px');
			} else {
				element.css('height', height + 'px');
			}
			//TODO
			$scope.$watch('$session.conv', function(){
				if(attrs.convId === $scope.$session.conv){
					var height = attrs.itemCount * attrs.itemHeight;
					if(attrs.itemCount > 1){
						height = height + 30;
					}
					if(height < attrs.minHeight){
						element.css('height', attrs.minHeight + 'px');
					} else {
						element.css('height', height + 'px');
					}
				} else {
					element.css('height', attrs.minHeight + 'px');
				}
			});
		}
	};
}]);