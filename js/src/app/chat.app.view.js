/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
window.Chat =  window.Chat || {};
Chat.app = Chat.app || {};
Chat.app.view = {
	addConv : function(convId, users, backend, msgs){
		Chat.scope.$apply(function(){
			Chat.scope.view.addConv(convId, users, backend, msgs);
		});
	},
	addChatMsg : function(convId, contact, msg, timestamp, backend, noNotify){
		Chat.scope.$apply(function(){
			Chat.scope.view.addChatMsg(convId, contact, msg, timestamp, backend, noNotify);
		});
	},
	addUserToConv : function(convId, user){
		Chat.scope.$apply(function(){
			Chat.scope.view.addUserToConv(convId, user);
		});
	},
	getBackends : function(key){
		var returnBackend;
		for(var index in Chat.scope.backends){
			var backend = Chat.scope.backends[index];
			if(key === backend.name){
				returnBackend = backend;
			}
		}
		return returnBackend;
	},
	replaceUsers : function(convId, users){
		Chat.scope.$apply(function(){
	 		Chat.scope.view.replaceUsers(convId, users);
		});
	},
	makeActive : function(convId, $event, exception){
		Chat.scope.$apply(function(){
			Chat.scope.view.makeActive(convId, $event, exception);
		});
	},
	makeUserOnline : function(userId){
		Chat.scope.$apply(function(){
			Chat.scope.view.makeUserOnline(userId);
		});
	},
	makeUserOffline : function(userId){
		Chat.scope.$apply(function(){
			Chat.scope.view.makeUserOffline(userId);
		});
	}
};