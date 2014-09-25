angular.module('chat').factory('activeUser', ['initvar', function(initvar) {
	return initvar.contactsObj[OC.currentUser];
}]);