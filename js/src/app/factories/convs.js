angular.module('chat').factory('convs', ['activeUser', 'contacts', '$filter', 'title', function(activeUser, contacts, $filter, title) {
	var convs = {};

	return {
		convs: convs, // DON NOT USE THIS! ONLY FOR ATTACHING TO THE SCOPE
		get : function(id) {
			return convs[id];
		},
		addConv : function(id, users, backend, msgs){
			//generate conv name + higher order of contacts
			var name  = '';
			for(var key in users){
				var user = users[key];
				if(user.id !== activeUser.id){
					name += user.displayname + ' ';
					var order = contacts.getHighestOrder();
					//$scope.contactsObj[user.id].order = order;
				}
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
					name : name
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
				var noNotify = false;
			}
			if(user.id !== activeUser.id && noNotify === false){
				title.notify(user.displayname);
			}

			//if(convId !== $scope.active.conv && noNotify === false){
			//	this ins't the active conv
			//	we have to notify the user of new messages in this conv
			//	$scope.view.notifyMsgInConv(convId);
			//}

			// Check if the user is equal to the user of the last msg
			// First get the last msg
			var contact = user;

			if(convs[convId].msgs[convs[convId].msgs.length -1] !== undefined){
				var lastMsg = convs[convId].msgs[convs[convId].msgs.length -1];
				if(lastMsg.contact.displayname === contact.displayname){
					// The current user send the last message
					// so don't readd the border etc

					if(Chat.app.util.isYoutubeUrl(lastMsg.msg) || Chat.app.util.isImageUrl(lastMsg.msg)){
						if (Chat.app.util.timeStampToDate(lastMsg.timestamp).minutes === Chat.app.util.timeStampToDate(timestamp).minutes
							&& Chat.app.util.timeStampToDate(lastMsg.timestamp).hours === Chat.app.util.timeStampToDate(timestamp).hours
							){
							convs[convId].msgs.push({
								contact : contact,
								msg : $.trim(msg),
								timestamp : timestamp,
								time : null,
							});
						} else {
							convs[convId].msgs.push({
								contact : contact,
								msg : $.trim(msg),
								timestamp : timestamp,
								time : Chat.app.util.timeStampToDate(timestamp),
							});
						}
					} else {
						lastMsg.msg = lastMsg.msg + "\n" + msg;
						convs[convId].msgs[convs[convId].msgs.length -1] = lastMsg;
					}
				} else if (Chat.app.util.timeStampToDate(lastMsg.timestamp).minutes === Chat.app.util.timeStampToDate(timestamp).minutes
					&& Chat.app.util.timeStampToDate(lastMsg.timestamp).hours === Chat.app.util.timeStampToDate(timestamp).hours
					) {
					convs[convId].msgs.push({
						contact : contact,
						msg : $.trim(msg),
						timestamp : timestamp,
						time : null,
					});
				} else {
					convs[convId].msgs.push({
						contact : contact,
						msg : $.trim(msg),
						timestamp : timestamp,
						time : Chat.app.util.timeStampToDate(timestamp),
					});
				}
			} else {
				convs[convId].msgs.push({
					contact : contact,
					msg : $.trim(msg),
					timestamp : timestamp,
					time : Chat.app.util.timeStampToDate(timestamp),
				});
			}

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
			for (firstConv in convs) break;
			if (typeof firstConv !== 'undefined') {
				return firstConv;
			} else {
				return undefined;
			}
		},
		makeActive : function(convId, $event, exception) {
			var scope = $('#app').scope();
			if (!scope.$$phase) {
				scope.$apply(function () {
					scope.view.makeActive(convId, $event, exception);
				});
			} else {
				scope.view.makeActive(convId, $event, exception);
			}
		}
	};
}]);
