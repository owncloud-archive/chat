angular.module('chat').filter('enhanceFiles', [function() {
    return function(text) {
        var imgRegex = /(\S*\.(?:gif|jpg|jpeg|tiff|png|svg|webp))/gi;

        var img = '<a href="/index.php/apps/files/ajax/download.php?dir=/&files=$1">' + '<img alt="image" height="150px" src="/index.php/apps/files/ajax/download.php?dir=/&files=$1"/></a>';
        text = text.replace(imgRegex, img);
        return text;
    };
}]);
