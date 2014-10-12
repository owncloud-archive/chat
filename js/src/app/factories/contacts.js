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
		}
	};
}]);