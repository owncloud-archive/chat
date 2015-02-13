angular.module('chat').directive('update', function () {
	return function (scope, element, attrs) {
		element.bind("keydown keypress", function (event) {
			if(event.which === 13) {
				scope.fields.chatMsg = element.val();
				scope.sendChatMsg();
			}
		});
	};
});