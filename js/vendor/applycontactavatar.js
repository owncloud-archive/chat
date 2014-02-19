(function ($) {
	$.fn.applyContactAvatar = function(addressbookBackend, addressBookId, id, displayname, size) {
		var $div = this;
		$div.height(size);
		$div.width(size);
		OC.Router.registerLoadedCallback(function() {
			var url = OC.Router.generate('contacts_contact_photo', {backend : addressbookBackend, addressBookId : addressBookId, contactId: id})+'?requesttoken='+oc_requesttoken;
			$.get(url, function(result) {
				if (typeof(result) === 'object') {
					$div.imageplaceholder(displayname);
				} else {
					$div.show();
					console.log('fail');
					$div.html('<img src="'+url+'">');
				}
			});
		});
	};
}(jQuery));
