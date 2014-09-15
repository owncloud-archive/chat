describe('Unit: ConvController', function(){
	beforeEach(module('chat'));

	var ConvController;
	var $scope;

	beforeEach(inject(function($controller, $rootscope){
		$scope = $rootscope.new();

		ConvController = $controller('ConvController', {
			$scope: $scope
		})
	}));

	it('should create a $scope.convs object', function(){
		expect($scope.convs).toEqual({});
	});

});