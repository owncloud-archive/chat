describe('Unit: ConvController', function(){
	var $rootScope;
	var $scope;
	var controller;

	beforeEach(function(){
		module('chat');

		inject(function($injector){
			$rootScope = $injector.get('$rootScope');
			$scope = $rootScope.$new();
			controller = $injector.get('$controller')('ConvController', {$scope: $scope});
		});
	});

	describe('Initialization', function(){
		it("Should instantiate convs to an object", function(){
			expect($scope.convs).toEqual({});
		});
	});

});