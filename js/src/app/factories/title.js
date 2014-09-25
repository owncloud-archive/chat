angular.module('chat').factory('title', [function() {
	var title;
	var default_title = 'Chat - ownCloud';
	var new_msgs = [];

	/**
	 * This will check if the window is active
	 *  if so
	 *   it will change the title of the page into 'New messages from {user}'
	 *  otherwise
	 *   it will empty the title
	 */
	return {
		/**
		 * This function will update the title of the page
		 * @param {string} newTitle
		 */
		updateTitle : function(newTitle){
			title = newTitle;
		},
		getDefaultTitle : function(){
			return default_title;
		},
		getTitle : function() {
			return title;
		},
		/**
		 * This function will add the user to the $scope.title.new_msgs array
		 * This way the user can be notified about a new msgs
		 * @param {string} user
		 */
		notify : function(user){
			if(new_msgs.indexOf(user) == -1){
				new_msgs.push(user);
			}
			var newTitle = 'New messages from ';
			if(new_msgs.length === 0 ){
				newTitle = '';
			} else {
				for (var key in new_msgs){
					var user = new_msgs[key];
					newTitle = newTitle + user + " ";
				}
			}
			this.updateTitle(newTitle);
		},
		emptyNewMsgs : function(){
			new_msgs = [];
			this.updateTitle('');
		}
	};
}]);