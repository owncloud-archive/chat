angular.module('chat').factory('contacts', ['$filter', 'initvar', function($filter, initvar) {
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
		}
	};
}]);