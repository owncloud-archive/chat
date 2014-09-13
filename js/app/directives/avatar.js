Chat.angular.directive('avatar', function() {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			element.applyContactAvatar(attrs.addressbookBackend, attrs.addressbookId, attrs.id, attrs.displayname, attrs.size);
			if(attrs.online !== undefined){
				element.online(attrs.isonline);
				$scope.$watch('contactsObj', function(){
					element.online(Chat.scope.contactsObj[attrs.id].online);
				}, true);
			}
		}
	};
})