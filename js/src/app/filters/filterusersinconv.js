angular.module('chat').filter('filterUsersInConv', ['activeConv', 'convs', function(activeConv, convs) {
	return function(contacts) {
		var result = [];
		var users = convs.get(activeConv()).users;
		angular.forEach(contacts, function(contact){
			if($.inArray(contact, users) === -1){
				result.push(contact);
			}
		});
		console.log(result);
		return result;
	};
}]);