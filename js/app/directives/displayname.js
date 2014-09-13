Chat.angular.directive('displayname', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			var text  = '';
			if(typeof attrs.users === 'string' ){
				users = JSON.parse(attrs.users);
			} else {
				users = attrs.users;
			}
			for (var key in users){
				var user = users[key];
				if(user.id !== Chat.scope.active.user.id){
					text += user.displayname + ' ';
				}
			}
			element.text(text);
		}
	};
})