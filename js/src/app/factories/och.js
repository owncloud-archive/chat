angular.module('chat').factory('och', ['activeUser', 'convs', 'contacts', function(activeUser, convs, contacts) {
	var api = {
		command: {
			join: function (convId, success) {
				api.util.doRequest({
					"type": "command::join::request",
					"data": {
						"conv_id": convId,
						"timestamp": Time.now(),
						"user": activeUser,
						"session_id": sessionId
					}
				}, success);
			},
			invite: function (userToInvite, convId, success) {
				api.util.doRequest({
					"type": "command::invite::request",
					"data": {
						"conv_id": convId,
						"timestamp": Time.now(),
						"user_to_invite": userToInvite,
						"user": activeUser,
						"session_id": sessionId
					}
				}, success);
			},
			sendChatMsg: function (msg, convId, success) {
				api.util.doRequest({
					"type": "command::send_chat_msg::request",
					"data": {
						"conv_id": convId,
						"chat_msg": msg,
						"user": activeUser,
						"session_id": sessionId,
						"timestamp": Time.now()
					}
				}, success);
			},
			online: function () {
				api.util.doRequest({
					"type": "command::online::request",
					"data": {
						"user": activeUser,
						"session_id": sessionId,
						"timestamp": Time.now()
					}
				}, function () {
				});
			},
			offline: function () {
				api.util.doSyncRequest({
					"type": "command::offline::request",
					"data": {
						"user": activeUser,
						"session_id": sessionId,
						"timestamp": Time.now()
					}
				}, function () {
				});
			},
			startConv: function (userToInvite, success) {
				api.util.doRequest({
					"type": "command::start_conv::request",
					"data": {
						"user": activeUser,
						"session_id": sessionId,
						"timestamp": Time.now(),
						"user_to_invite": userToInvite
					}
				}, success);
			},
			getMessages: function (convId, startpoint, success) {
				api.util.doRequest({
					"type": "data::messages::request",
					"data": {
						"user": activeUser,
						"session_id": sessionId,
						"conv_id": convId,
						"startpoint": startpoint
					}
				}, success);
			},
			getUsers: function (convId, success) {
				api.util.doRequest({
					"type": "data::get_users::request",
					"data": {
						"user": activeUser,
						"session_id": sessionId,
						"conv_id": convId
					}
				}, success);
			}
		},
		on: {
			invite: function (data) {
				// Here update the view
				var backend = Chat.app.view.getBackends('och');
				var convId = data.conv_id;
				// TODO check if data.user is a user or a contact
				if (convs.get(convId) === undefined) {
					api.command.join(data.conv_id, function (dataJoin) {
						// After we joined we should update the users array with all users in this conversation
						var users = dataJoin.data.users;
						var msgs = dataJoin.data.messages;
						convs.addConv(convId, users, backend, msgs);
					});
				}
			},
			chatMessage: function (data) {
				convs.addChatMsg(data.conv_id, data.user, data.chat_msg,
					data.timestamp, 'och');
			},
			joined: function (data) {
				//Chat.scope.$apply(function(){
				//	Chat.scope.view.replaceUsers();
				//});
					convs.replaceUsers(data.conv_id, data.users);
			},
			online: function (data) {
				contacts.markOnline(data.user.id);
			},
			offline: function (data) {
				contacts.markOffline(data.user.id);
			}
		},
		util: {
			doRequest: function (request, success) {
				$.ajax({
					type: "POST",
					url: OC.generateUrl('/apps/chat/och/api'),
					data: JSON.stringify(request),
					headers: {'Content-Type': 'application/json'}
				}).always(function (data) {
					success(data);
				});
			},
			doSyncRequest: function (request, success, error) {
				$.ajax({
					type: "POST",
					url: OC.generateUrl('/apps/chat/och/api'),
					data: JSON.stringify(request),
					headers: {'Content-Type': 'application/json'},
					async: true
				});
			},
			longPoll: function () {
				api.util.getPushMessages(function (data) {
					var ids_del = [];
					for (var push_id in data.push_msgs) {
						var push_msg = data.push_msgs[push_id];
						ids_del.push(push_id);
						api.util.handlePushMessage(push_msg);
					}
					api.util.deletePushMessages(ids_del, function () {
						api.util.longPoll();
					});
				});
			},
			handlePushMessage: function (push_msg) {
				if (push_msg.type === "invite") {
					api.on.invite(push_msg.data);
				} else if (push_msg.type === "send_chat_msg") {
					api.on.chatMessage(push_msg.data);
				} else if (push_msg.type === "joined") {
					api.on.joined(push_msg.data);
				} else if (push_msg.type === "online") {
					api.on.online(push_msg.data);
				} else if (push_msg.type === "offline") {
					api.on.offline(push_msg.data);
				}
			},
			getPushMessages: function (success) {
				api.util.doRequest({
					"type": "push::get::request",
					"data": {
						"user": activeUser,
						"session_id": sessionId
					}
				}, success);
			},
			deletePushMessages: function (ids, success) {
				api.util.doRequest({
					"type": "push::delete::request",
					"data": {
						"user": activeUser,
						"session_id": sessionId,
						ids: ids
					}
				}, function (data) {
					success();
				});
			}
		},
		INVALID_HTTP_TYPE : 0,
		COMMAND_NOT_FOUND : 1,
		PUSH_ACTION_NOT_FOUND : 2,
		DATA_ACTION_NOT_FOUND : 3,
		NO_SESSION_ID : 6,
		USER_NOT_EQUAL_TO_OC_USER : 7,
		NO_TIMESTAMP : 8,
		NO_CONV_ID : 9,
		NO_USER_TO_INVITE : 10,
		USER_EQUAL_TO_USER_TO_INVITE : 11,
		USER_TO_INVITE_NOT_OC_USER : 12,
		NO_CHAT_MSG : 13
	};
	var sessionId;
	var initConvs;
	return {
		init : function(s, i){
			sessionId = s;
			initConvs = i;
			api.util.longPoll();
			setInterval(api.command.online, 6000);
		},
		quit : function(){
			api.command.offline();
		},
		sendChatMsg : function(convId, msg){
			api.command.sendChatMsg(msg, convId, function(){});
		},
		invite : function(convId, userToInvite, groupConv, callback){
			if(groupConv){
				// We are in a group conversation
				api.command.invite(userToInvite, convId, callback);
			} else {
				var users = [];
				for (var key in convs.get(convId).users) {
					users.push(convs.get(convId).users[key]);
				}
				users.push(userToInvite);
				this.newConv(users, callback);
			}
		},
		newConv : function(userToInvite, success){
			api.command.startConv(
				userToInvite,
				success
			);
		}
	};
}]);