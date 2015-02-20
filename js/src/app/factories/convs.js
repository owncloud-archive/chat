angular.module('chat').factory('convs', ['contacts', '$filter', 'title', 'session', '$injector', 'time', function(contacts, $filter, title, $session, $injector, Time) {
	var convs = {};

	return {
		convs: convs, // DON NOT USE THIS! ONLY FOR ATTACHING TO THE SCOPE
		get : function(id) {
			return convs[id];
		},
		addConv : function(id, users, backend, msgs, files){
			//generate conv name + higher order of contacts
			var name  = '';
			for(var key in users){
				var user = users[key];
				if(user.id !== $session.user.id){
					name += user.displayname + ' ';
					var order = contacts.getHighestOrder();
					//$scope.contactsObj[user.id].order = order;
				}
			}

			// check if backend is a string
			if (typeof backend === "string"){
				var backends = $injector.get('backends');
				backend = backends[backend];
			}

			// end generate conv name
			if(convs[id] === undefined) {
				//get highest order
				var order = this.getHighestOrder();
				convs[id] = {
					id : id,
					users : users,
					msgs : [],
					backend : backend,
					new_msg : false,
					raw_msgs : [],
					order : order,
					name : name,
					files : files
				};
				this.makeActive(id);
				if(msgs !== undefined){
					for (var key in msgs){
						var msg = msgs[key];
						this.addChatMsg(id, contacts[msg.user], msg.msg, msg.timestamp, backend);
					}
				}
			}
		},
		getHighestOrder : function(){
			var sortedConvs = $filter('orderObjectBy')(convs, 'order');
			if(sortedConvs[sortedConvs.length - 1] !== undefined){
				return sortedConvs[sortedConvs.length - 1].order + 1;
			} else {
				return 1;
			}
		},
		/**
		 * This will add an chat msg to an existing conversation
		 * This will call the notify() function when the message isn't send by the active user
		 * This will call the notifyMsgInConv() function when it isn't the active conv
		 * This will make the order of the conv the highest
		 * @param {string} convId
		 * @param {object} user
		 * @param {string} msg
		 * @param {int} timestamp
		 * @param {object} backend
		 * @param {bool} noNotify
		 */
		addChatMsg : function(convId, user, msg, timestamp, backend, noNotify){
			if(noNotify === undefined){
				noNotify = false;
			}
			if(user.id !== $session.user.id && noNotify === false){
				title.notify(user.displayname);
			}

			if(convId !== $session.conv && noNotify === false){
			//	this ins't the active conv
			//	we have to notify the user of new messages in this conv
				this.notifyMsgInConv(convId);
			}

			// Check if the user is equal to the user of the last msg
			// First get the last msg
			var contact = user;
			convs[convId].msgs.push({
				contact : contact,
				msg : msg,
				timestamp : timestamp,
				time : Time.timestampToObject(timestamp),
				time_read : Time.format(timestamp),
			});

			// Add raw msgs to raw_msgs
			convs[convId].raw_msgs.push({"msg" : msg, "timestamp" : timestamp, "user" : user});
			convs[convId].order = this.getHighestOrder() +1;
		},
		/**
		 * This will replace the users in an existing conversation
		 * @param {string} convId
		 * @param {array} users - array with objects
		 */
		replaceUsers : function(convId, users){
			convs[convId].users = users;
		},
		/**
		 * This will bold the conversation name in the view
		 * @param {string} convId
		 */
		notifyMsgInConv : function(convId){
			convs[convId].new_msg = true;
		},
		/**
		* This will add an user to an conversation
		* @param {string} convId
		* @param {object} user
		*/
		addUserToConv : function(convId, user){
			if(convs[convId].users.indexOf(user) === -1){
				convs[convId].users.push(user);
			}
		},
		/**
		 * This function will return the first conversation in the conversation list
		 * @returns {object|undefined}
		 */
		getFirstConv : function(){
			for (var firstConv in convs) break;
			if (typeof firstConv !== 'undefined') {
				return firstConv;
			} else {
				return undefined;
			}
		},
		makeActive : function(convId, $event, exception) {
			$session.conv = convId;
			this.convs[convId].new_msg = false;
			$('#chat-msg-input-field').focus();
		},
		attachFile : function(convId, path, timestamp, user){
			if(timestamp === undefined){
				timestamp = Time.now();
			}
			convs[convId].files.push({
				"path": path,
				"user": user,
				"timestamp" : timestamp
			});
			this.addChatMsg(convId, user,  tran('translations-attached', {displayname: user.displayname, path: path}),
				timestamp, 'och');
		},
		removeFile : function(convId, path, timestamp, user, key){
			convs[convId].files.splice(key, 1);
            this.addChatMsg(convId, user,  tran('translations-removed', {displayname: user.displayname, path: path}),
                timestamp, 'och');
		},
		exists: function (convId) {
			if (typeof convs[convId] === 'undefined'){
				return false;
			} else {
				return true;
			}
		},
	};
}]);
