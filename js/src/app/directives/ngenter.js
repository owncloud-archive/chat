angular.module('chat').directive('ngEnter', function () {
	return function (scope, element, attrs) {
		element.bind("keydown keypress", function (event) {
			if(event.which === 13) {
				if (event.shiftKey === false){
					scope.$apply(function (){
						scope.$eval(attrs.ngEnter);
					});
					event.preventDefault();
				}
			}
		});
	};
})