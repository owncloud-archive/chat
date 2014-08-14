/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
(function ($) {
    $.fn.applyContactAvatar = function(addressbookBackend, addressBookId, id, displayname, size) {
        var $div = this;
        var cacheTime = Cache.day(1);
		$div.height(size);
        $div.width(size);

        // First generate an id of this contact which is used in the cache
        var cacheId = id;
		// Next check if the cacheId occurs in the cache
		var value = Cache.get(cacheId)
		if(value !== undefined){
            if (value.noAvatar == true) {
                $div.imageplaceholder(displayname);
            } else {
				var url = value.base64 + "?requesttoken=" + oc_requesttoken;
                $div.show();
				$div.html('<img width="' + size + '" height="' + size + '" src="'+ url +'">');
			}
        } else {
			var url = OC.generateUrl(
				'/avatar/{user}/{size}?requesttoken={requesttoken}',
				{user: id, size: size * window.devicePixelRatio, requesttoken: oc_requesttoken});
			$.get(url, function(result) {
				if (typeof(result) === 'object') {
                    Cache.set(cacheId, {"noAvatar" : true}, cacheTime);
                    $div.imageplaceholder(displayname);
                } else {
					var cacheUrl = OC.generateUrl('/avatar/{user}/{size}',{user: id, size: size * window.devicePixelRatio});
					Cache.set(cacheId, {"noAvatar" : false, "base64" : cacheUrl}, cacheTime);
                    $div.show();
					$div.html('<img width="' + size + '" height="' + size + '" src="'+url+'">');
				}
            });
        }
    };
}(jQuery));
