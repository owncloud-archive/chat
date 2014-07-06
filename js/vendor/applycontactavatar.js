(function ($) {
    $.fn.applyContactAvatar = function(addressbookBackend, addressBookId, id, displayname, size) {
        var $div = this;
        $div.height(size);
        $div.width(size);

        // First generate an id of this contact which is used in the cache
        var cacheId = addressbookBackend + ":" + addressBookId + ":" + id;
//        Next check if the cacheId occurs in the cache
        if(Chat.app.cache.avatar[cacheId] !== undefined){
            if (Chat.app.cache.avatar[cacheId] === '') {
                $div.imageplaceholder(displayname);
            } else {
                $div.show();
				$div.html('<img src="data:image/gif;base64,' + Chat.app.cache.avatar[cacheId] + '" >');
            }
        } else {
			var url = '/index.php' + OC.linkTo('contacts', 'addressbook/' + addressbookBackend + '/' + addressBookId + '/contact/' + id +'/photo/base64?requesttoken='+oc_requesttoken);
            $.get(url, function(result) {
                if (result.data === '') {
                    Chat.app.cache.avatar[cacheId] = '';
                    $div.imageplaceholder(displayname);
                } else {
					Chat.app.cache.avatar[cacheId] = result.data;
                    $div.show();
//            <img width="16" height="16" alt="star" src="data:image/gif;base64,R0lGODlhEAAQAMQAAORHHOVSKudfOulrSOp3WOyDZu6QdvCchPGolfO0o/XBs/fNwfjZ0frl3/zy7////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAkAABAALAAAAAAQABAAAAVVICSOZGlCQAosJ6mu7fiyZeKqNKToQGDsM8hBADgUXoGAiqhSvp5QAnQKGIgUhwFUYLCVDFCrKUE1lBavAViFIDlTImbKC5Gm2hB0SlBCBMQiB0UjIQA7"
// />
                    $div.html('<img src="data:image/gif;base64,' + result.data + '" >');
//					$div.css('background-image', 'url(data:image/png;base64,' + result.data + ')');
				}
            });
        }
    };
}(jQuery));
