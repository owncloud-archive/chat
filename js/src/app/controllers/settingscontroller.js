/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
angular.module('chat').controller(
	'SettingsController',
	[
		'$scope',
		'$interval',
		'backends',
		'session',
		'$http',
		function(
			$scope,
			initvar,
			backends,
			$session,
			$http
		){

			Chat.settings = $scope;

			$scope.backends = backends;


			$scope.status = null;

			$scope.save = function(){
				$scope.status = 'saving';
				$http.post(OC.generateUrl('/apps/chat/config/set'), {backends: $scope.backends}).
					success(function(data, status, headers, config) {
						$scope.status = 'saved';
					}).
					error(function(data, status, headers, config) {
						$scope.status = 'error';
					});
			};
		}
	]
);

