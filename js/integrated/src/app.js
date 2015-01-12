angular.module('chat', ['ngSanitize', 'bernhardposselt.enhancetext']);
angular.module('chat').config(['enhanceTextFilterProvider', '$httpProvider', function (enhanceTextFilterProvider, $httpProvider) {
	enhanceTextFilterProvider.setOptions({
		embeddedImagesHeight: '150px'
	});
	$httpProvider.defaults.headers.common.requesttoken = oc_requesttoken;
	emojione.sprites = true;
	emojione.ascii = true;
}]);
$(document).ready(function(){
	$.post('/index.php/apps/chat/initvar', function (data) {
		window.initVar = data;
		$.get('/apps/chat/integrated.php', function ($chatHTML) {
			angular.module('chat').run(function () {
				document.body.innerHTML += $chatHTML;
				var $compile = angular.injector(['ng']).get('$compile');
				var $rootScope = angular.injector(['ng']).get('$rootScope');
				document.addEventListener("DOMContentLoaded", function (event) {
					$compile($chatHTML)($rootScope);
				});
			});
			angular.bootstrap(document, ["chat"]);
		});
	});
});
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