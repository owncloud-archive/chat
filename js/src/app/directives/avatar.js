angular.module('chat').directive('avatar', ['contacts', function(contacts) {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			element.applyContactAvatar(attrs.addressbookBackend, attrs.addressbookId, attrs.id, attrs.displayname, attrs.size);
			if(attrs.online !== undefined){
				element.online(attrs.isonline);
				$scope.$watch('contacts', function(){
					element.online(contacts.contacts[attrs.id].online);
				}, true);
			}
		}
	};
}]);