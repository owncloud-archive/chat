angular.module('chat').factory('activeConv', [function(){

	return function(){
		var scope = $('#app').scope();
		return scope.active.conv;
	}

}]);