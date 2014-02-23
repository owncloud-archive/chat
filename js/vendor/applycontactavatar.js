(function ($) {
    $.fn.applyContactAvatar = function(addressbookBackend, addressBookId, id, displayname, size) {
        var $div = this;
        $div.height(size);
        $div.width(size);

        // First generate an id of this contact which is used in the cache
        var cacheId = addressbookBackend + ":" + addressBookId + ":" + id;
        // Next check if the cacheId occurs in the cache
        if(Chat.app.cache.avatar[cacheId] !== undefined){
            if (typeof(Chat.app.cache.avatar[cacheId]) === 'object') {
                $div.imageplaceholder(displayname);
            } else {
                $div.show();
                $div.html('<img src="'+Chat.app.cache.avatar[cacheId]+'">');
            }
        } else {
            var url = '/index.php' + OC.linkTo('contacts', 'addressbook/' + addressbookBackend + '/' + addressBookId + '/contact/' + id +'/photo?requesttoken='+oc_requesttoken);
            $.get(url, function(result) {
                if (typeof(result) === 'object') {
                    Chat.app.cache.avatar[cacheId] = {};
                    $div.imageplaceholder(displayname);
                } else {
                    Chat.app.cache.avatar[cacheId] = url;
                    $div.show();
                    $div.html('<img src="'+Chat.app.cache.avatar[cacheId]+'">');
                }
            });
        }
    };
}(jQuery));
