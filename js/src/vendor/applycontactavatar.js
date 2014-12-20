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
				applyAvatar(value.base64 + "?requesttoken=" + oc_requesttoken, size);
			}
		} else {
			// check if the contact is an ownCloud user or a contact
			if (addressbookBackend === 'local' && addressBookId === ""){
				// ownCloud user
				var url = OC.generateUrl(
					'/avatar/{user}/{size}?requesttoken={requesttoken}',
					{user: id, size: size * window.devicePixelRatio, requesttoken: oc_requesttoken});
				var urlWithoutRT = OC.generateUrl(
					'/avatar/{user}/{size}',
					{user: id, size: size * window.devicePixelRatio});
				$.get(url, function(result) {
					if (typeof(result) === 'object') {
						Cache.set(cacheId, {"noAvatar" : true}, cacheTime);
						$div.imageplaceholder(displayname);
					} else {
						Cache.set(cacheId, {"noAvatar" : false, "base64" : urlWithoutRT}, cacheTime);
						applyAvatar(url, size);
					}
				});
			} else {
				var url = OC.generateUrl('/apps/contacts/addressbook/{backend}/{addressbook_id}/contact/{contact_id}/photo?requesttoken={requesttoken}',
					{backend: addressbookBackend, contact_id: id, addressbook_id: addressBookId, requesttoken: oc_requesttoken});
				var urlWithoutRT = OC.generateUrl('/apps/contacts/addressbook/{backend}/{addressbook_id}/contact/{contact_id}/photo',
					{backend: addressbookBackend, contact_id: id, addressbook_id: addressBookId});
				$.get(url, function(result) {
					if (typeof(result) === 'object') {
						Cache.set(cacheId, {"noAvatar" : true}, cacheTime);
						$div.imageplaceholder(displayname);
					} else {
						Cache.set(cacheId, {"noAvatar" : false, "base64" : url}, cacheTime);
						applyAvatar(url, size);
					}
				});
			}

		}

		function applyAvatar(url, size){
			$div.show();
			$div.html('<img width="' + size + '" height="' + size + '" src="'+ url +'">');
		}
	};
}(jQuery));
