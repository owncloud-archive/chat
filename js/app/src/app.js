/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
	//$compileProvider.debugInfoEnabled(false);

angular.module('chat', ['ngSanitize', 'bernhardposselt.enhancetext']);
angular.module('chat').config(['enhanceTextFilterProvider', '$httpProvider', '$compileProvider', function (enhanceTextFilterProvider, $httpProvider, $compileProvider) {
	enhanceTextFilterProvider.setOptions({
		embeddedImagesHeight: '150px'
	});
	$httpProvider.defaults.headers.common.requesttoken = oc_requesttoken;
	emojione.sprites = true;
	emojione.ascii = true;
}]);
function tran(id, vars){
	var text = 	$('#' + id).text();
	var _build = function (text, vars) {
		return text.replace(/{([^{}]*)}/g,
			function (a, b) {
				var r = vars[b];
				return typeof r === 'string' || typeof r === 'number' ? r : a;
			}
		);
	};
	return _build(text, vars);
}