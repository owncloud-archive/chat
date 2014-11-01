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

			$scope.save = function(){
				console.log($scope.backends.xmpp.config);
				$http.post(OC.generateUrl('/apps/chat/config/set'), {backends: $scope.backends}).
					success(function(data, status, headers, config) {
						alert('success');
						console.log(status);
					}).
					error(function(data, status, headers, config) {
						alert('error');
					});
			};
		}
	]
);

