angular.module('chat').filter('userFilter', function() {
	return function(users) {
		var output = [];
		for (var key in users){
			var user = users[key];
			if(user.id !== OC.currentUser){
				output.push(user);
			}
		}
		return output;
	};
})