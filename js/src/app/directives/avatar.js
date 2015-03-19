angular.module('chat').directive('avatar', ['contacts', function(contacts) {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			applyContactAvatar(element, attrs.addressbookBackend, attrs.addressbookId, attrs.id, attrs.displayname, attrs.size);
			if(attrs.online !== undefined){
				element.online(attrs.isonline);
				$scope.$watch('contacts', function(){
					element.online(contacts.contacts[attrs.id].online);
				}, true);
			}
		}
	};

	function applyContactAvatar(element, addressbookBackend, addressBookId, id, displayname, size){
		var cacheTime = Cache.day(1);
		element.height(size);
		element.width(size);

		// First generate an id of this contact which is used in the cache
		var cacheId = id;
		// Next check if the cacheId occurs in the cache
		var value = Cache.get(cacheId)
		if (value !== undefined) {
			if (value.noAvatar == true) {
				element.imageplaceholder(displayname);
			} else {
				applyAvatar(value.base64 + "?requesttoken=" + oc_requesttoken, size);
			}
		} else {
			// check if the contact is an ownCloud user or a contact
			if (addressbookBackend === 'local' && addressBookId === "-1") {
				// This is a NOT saved contact
				element.imageplaceholder(displayname);
			}
			else if (addressbookBackend === 'local' && addressBookId === "") {
				// ownCloud user
				var url = OC.generateUrl(
					'/avatar/{user}/{size}?requesttoken={requesttoken}',
					{
						user: id,
						size: size * window.devicePixelRatio,
						requesttoken: oc_requesttoken
					});
				var urlWithoutRT = OC.generateUrl(
					'/avatar/{user}/{size}',
					{user: id, size: size * window.devicePixelRatio});
				$.get(url, function (result) {
					if (typeof(result) === 'object') {
						Cache.set(cacheId, {"noAvatar": true}, cacheTime);
						element.imageplaceholder(displayname);
					} else {
						Cache.set(cacheId, {
							"noAvatar": false,
							"base64": urlWithoutRT
						}, cacheTime);
						applyAvatar(url, size);
					}
				});
			} else {
				var url = OC.generateUrl('/apps/contacts/addressbook/{backend}/{addressbook_id}/contact/{contact_id}/photo?requesttoken={requesttoken}',
					{
						backend: addressbookBackend,
						contact_id: id,
						addressbook_id: addressBookId,
						requesttoken: oc_requesttoken
					});
				var urlWithoutRT = OC.generateUrl('/apps/contacts/addressbook/{backend}/{addressbook_id}/contact/{contact_id}/photo',
					{
						backend: addressbookBackend,
						contact_id: id,
						addressbook_id: addressBookId
					});
				$.get(url, function (result) {
					if (typeof(result) === 'object') {
						Cache.set(cacheId, {"noAvatar": true}, cacheTime);
						element.imageplaceholder(displayname);
					} else {
						Cache.set(cacheId, {
							"noAvatar": false,
							"base64": url
						}, cacheTime);
						applyAvatar(url, size);
					}
				});
			}

		}
	}
	function applyAvatar(url, size){
		element.show();
		element.html('<img width="' + size + '" height="' + size + '" src="'+ url +'">');
	}
}]);