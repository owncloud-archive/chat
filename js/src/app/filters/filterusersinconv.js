angular.module('chat').filter('filterUsersInConv', ['activeConv', 'convs', function(activeConv, convs) {
	return function(contacts) {
		var result = [];
		var users = convs.get(activeConv()).users;
		var usersId = [];
        for (var key in users){
            var user = users[key];
            usersId.push(user.id);
        }
        for (var key in contacts){
            var contact = contacts[key];
            if($.inArray(contact.id, usersId) === -1){
                result.push(contact);
            }
        }
		return result;
    };
}]);