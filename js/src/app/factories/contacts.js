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
		addContacts: function (contacts) {
			$http.post(OC.generateUrl('/apps/chat/contacts/add/'), {contacts: contacts}).
				success(function(data, status, headers, config) {
					$.extend(this.contacts, data);
				}).
				error(function(data, status, headers, config) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
				});
		}
	};
}]);