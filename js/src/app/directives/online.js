angular.module('chat').directive('online', ['contacts', function(contacts) {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			if(contacts.contacts[attrs.id].online === 'true' || contacts.contacts[attrs.id].online === true){
				element.addClass('online-dot');
			} else {
				element.removeClass('online-dot');
			}
			$scope.$watch('contacts', function(){
				if(contacts.contacts[attrs.id].online === 'true' || contacts.contacts[attrs.id].online === true){
					element.addClass('online-dot');
				} else {
					element.removeClass('online-dot');
				}
			}, true);
		}
	};
}]);