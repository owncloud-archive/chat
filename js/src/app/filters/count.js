angular.module('chat').filter('count', function() {
    return function(items) {
        var count = 0;
        for (var key in items){
            count++;
        }
        return count;
    };
});