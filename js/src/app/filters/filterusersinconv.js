angular.module('chat').filter('filterUsersInConv', function() {
	return function(contacts) {
		var result = [];
		var users = Chat.scope.convs[Chat.scope.active.conv].users;
		angular.forEach(contacts, function(contact){
			if($.inArray(contact, users) === -1){
				result.push(contact);
			}
		});

		return result;
	};
});