angular.module('chat').directive('online', ['contacts', function(contacts) {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			element.online(attrs.isonline);
			$scope.$watch('contacts', function(){
				element.online(contacts.contacts[attrs.id].online);
			}, true);
		}
	};
}]);