angular.module('chat').factory('contacts', ['$filter', 'initvar', '$http', function($filter, initvar, $http) {
	return {
		contacts : initvar.contactsObj,
		getHighestOrder : function(){
			var sortedContacts = $filter('orderObjectBy')(this.contacts, 'order');
			return sortedContacts[sortedContacts.length - 1].order + 1;
		},

		/**
		 * This will flag a contact in the $scope.contactsObj as online
		 * @param {string} userId
		 */
		markOnline : function(id){
			this.contacts[id].online = true;
		},
		/**
		* This will flag a contact in the $scope.contactsObj as offline
		* @param {string} userId
		*/
		markOffline : function(id){
			this.contacts[id].online = false;
		},
		findByBackendValue : function(backendId, value){
			for (var key in this.contacts){
				var contact = this.contacts[key];
				for (var backendKey in contact.backends){
					var backend = contact.backends[backendKey];

					if (backend.id === backendId && backend.value === value){
						console.log('contact ' + key);
						return this.contacts[key];
					}
				}
			}
			return false;
		},
		/**
		 * @param backendId The backend id
		 * @return array The contacts which supports this backend
		 */
		findByBackend : function (backendId) {
			var result = [];
			for (var key in this.contacts){
				var contact = this.contacts[key];
				for (var backendKey in contact.backends){
					var backend = contact.backends[backendKey];
					if (backend.id === backendId){
						result[key] = contact;
					}
				}
			}
			return result;
		},
		addContacts: function (contacts, success) {
			$backend = this;
			$http.post(OC.generateUrl('/apps/chat/contacts/add/'), {contacts: contacts}).
				success(function(data, status, headers, config) {
					$.extend($backend.contacts, data);
					success();
				}).
				error(function(data, status, headers, config) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
				});
		},
		removeContacts: function (contacts, success) {
			$backend = this;
			$http.post(OC.generateUrl('/apps/chat/contacts/remove/'), {contacts: contacts}).
				success(function(data, status, headers, config) {
					//$.extend($backend.contacts, data);
					//success();
					alert('done');
				}).
				error(function(data, status, headers, config) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
				});
		},
		/**
		 * @return array the current ownCloud user as contact
		 */
		self : function () {
			return this.contacts[OC.currentUser];
		},
		/**
		 * This method is used to generate a contact to use in the active session.
		 * This method is not used to generate a contact to save in the Contacts app/DB
		 * @param id integer|string the id of the contact
		 * @param online bool online state of the contact
		 * @param displayName string
		 * @param backends array
		 * @return object
		 */
		generateTempContact : function (id, online, displayName, backends) {
			return {
				"id" : id,
				"online": online,
				"displayname" : displayName,
				"order" : 0,
				"backends": backends,
				"address_book_id": "-1",
				"address_book_backend": "local",
				"saved": false
			};
		}
	};
}]);