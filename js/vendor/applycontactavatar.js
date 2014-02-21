(function ($) {
	$.fn.applyContactAvatar = function(addressbookBackend, addressBookId, id, displayname, size) {
		var $div = this;
		$div.height(size);
		$div.width(size);
		var url = '/index.php' + OC.linkTo('contacts', 'addressbook/' + addressbookBackend + '/' + addressBookId + '/contact/' + id +'/photo?requesttoken='+oc_requesttoken);
		$.get(url, function(result) {
			if (typeof(result) === 'object') {
				$div.imageplaceholder(displayname);
			} else {
				$div.show();
				console.log('fail');
				$div.html('<img src="'+url+'">');
			}
		});
	};
}(jQuery));
