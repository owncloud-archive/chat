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
				$div.html('<img width="' + size + '" height="' + size + '" src="'+Chat.app.cache.avatar[cacheId]+'">');

			}
        } else {
			var url = OC.generateUrl(
				'/avatar/{user}/{size}?requesttoken={requesttoken}',
				{user: id, size: size * window.devicePixelRatio, requesttoken: oc_requesttoken});
			$.get(url, function(result) {
				if (typeof(result) === 'object') {
                    Chat.app.cache.avatar[cacheId] = '';
                    $div.imageplaceholder(displayname);
                } else {
					Chat.app.cache.avatar[cacheId] = url;
                    $div.show();
					$div.html('<img width="' + size + '" height="' + size + '" src="'+url+'">');
				}
            });
        }
    };
}(jQuery));
