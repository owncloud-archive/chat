/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
Chat.och.api = {
	command : {
		join : function(convId, success) {
			Chat.och.api.util.doRequest({
				"type" : "command::join::request",
				"data" : {
					"conv_id" : convId,
					"timestamp" : Time.now(),
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId
				}
			}, success);
		},
		invite : function(userToInvite, convId, success) {
			Chat.och.api.util.doRequest({
				"type" : "command::invite::request",
				"data" : {
					"conv_id" : convId,
					"timestamp" : Time.now(),
					"user_to_invite" : userToInvite,
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId
				}
			}, success);
		},
		sendChatMsg : function(msg, convId, success) {
			Chat.och.api.util.doRequest({
				"type" : "command::send_chat_msg::request",
				"data" : {
					"conv_id" : convId,
					"chat_msg" : msg,
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					"timestamp" : Time.now()
				}
			}, success);
		},
		online : function() {
			Chat.och.api.util.doRequest({
				"type" : "command::online::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					"timestamp" : Time.now()
				}
			}, function() {
			});
		},
		offline : function() {
			Chat.och.api.util.doSyncRequest({
				"type" : "command::offline::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					"timestamp" : Time.now()
				}
			}, function() {
			});
		},
		startConv : function(userToInvite, success) {
			Chat.och.api.util.doRequest({
				"type" : "command::start_conv::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					"timestamp" : Time.now(),
					"user_to_invite" : userToInvite
				}
			}, success);
		},
		getMessages : function(convId, startpoint, success) {
			Chat.och.api.util.doRequest({
				"type" : "data::messages::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					"conv_id" : convId,
					"startpoint" : startpoint
				}
			}, success);
		},
		getUsers : function(convId, success){
			Chat.och.api.util.doRequest({
				"type" : "data::get_users::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					"conv_id" : convId
				}
			}, success);
		},
		archive : function(convId, success){
			Chat.och.api.util.doRequest({
				"type" : "command::archive::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					"conv_id" : convId
				}
			}, success);
		},
		unArchive : function(convId, success){
			Chat.och.api.util.doRequest({
				"type" : "command::un_archive::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					"conv_id" : convId
				}
			}, success);
		}
	},
	on : {
		invite : function(data) {
			// Here update the view
			var backend = Chat.app.view.getBackends('och');
			var convId = data.conv_id;
			// TODO check if data.user is a user or a contact
			if(Chat.scope.convs[convId] === undefined){
				Chat.och.api.command.join(data.conv_id, function(dataJoin) {
					// After we joined we should update the users array with all users in this conversation
					var users = dataJoin.data.users;
					var msgs = dataJoin.data.messages;
					Chat.app.view.addConv(convId, users, backend, msgs);
				});
			}
		},
		chatMessage : function(data) {
			Chat.app.view.addChatMsg(data.conv_id, data.user, data.chat_msg,
					data.timestamp, 'och');
		},
		joined : function(data){
			Chat.app.view.replaceUsers(data.conv_id, data.users);
		},
		online : function(data){
			Chat.app.view.makeUserOnline(data.user.id);
		},
		offline : function(data){
			Chat.app.view.makeUserOffline(data.user.id);
		}
	},
	util : {
		doRequest : function(request, success) {
			$.ajax({
				type: "POST",
				url: OC.generateUrl('/apps/chat/och/api'),
				data: JSON.stringify(request),
				headers: {'Content-Type' : 'application/json'}
			}).always(function(data){
				success(data);
			});
		},
		doSyncRequest : function(request, success, error) {
			$.ajax({
				type: "POST",
				url: OC.generateUrl('/apps/chat/och/api'),
				data: JSON.stringify(request),
				headers: {'Content-Type' : 'application/json'},
				async: true
			});
		},
		longPoll : function() {
			this.getPushMessages(function(data) {
				var ids_del = [];
				angular.forEach(data.push_msgs, function(push_msg, push_id) {
					ids_del.push(push_id);
					Chat.och.api.util.handlePushMessage(push_msg);
				});
				Chat.och.api.util.deletePushMessages(ids_del, function() {
					Chat.och.api.util.longPoll();
				});
			});
		},
		handlePushMessage : function(push_msg) {
			if (push_msg.type === "invite") {
				Chat.och.api.on.invite(push_msg.data);
			} else if (push_msg.type === "send_chat_msg") {
				Chat.och.api.on.chatMessage(push_msg.data);
			} else if (push_msg.type === "joined") {
				Chat.och.api.on.joined(push_msg.data);
			} else if (push_msg.type === "online") {
				Chat.och.api.on.online(push_msg.data);
			} else if (push_msg.type === "offline") {
				Chat.och.api.on.offline(push_msg.data);
			}
		},
		getPushMessages : function(success) {
			Chat.och.api.util.doRequest({
				"type" : "push::get::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId
				}
			}, success);
		},
		deletePushMessages : function(ids, success) {
			Chat.och.api.util.doRequest({
				"type" : "push::delete::request",
				"data" : {
					"user" : Chat.scope.active.user,
					"session_id" : Chat.och.sessionId,
					ids : ids
				}
			}, function(data) {
				success();
			});
		},
	}
};