Chat.angular = angular.module('chat', ['ngSanitize', 'bernhardposselt.enhancetext']);
Chat.angular.config(['enhanceTextFilterProvider', function (enhanceTextFilterProvider) {
    enhanceTextFilterProvider.setOptions({
    	embeddedImagesHeight: '150px'
    });
}]);