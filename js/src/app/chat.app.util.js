/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
window.Chat =  window.Chat || {};
Chat.app = Chat.app || {};
Chat.app.util = {
	timeStampToDate : function(timestamp){
		var date = new Date(timestamp * 1000);
		var hours = date.getHours();
		var minutes = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
		var seconds = date.getSeconds();
		return	{hours : date.getHours(), minutes : (date.getMinutes() < 10 ? '0' : '') + date.getMinutes(), seconds : date.getSeconds()};
	},
	isYoutubeUrl : function(url) {
		if(url.indexOf('https://youtube.com') > -1
			|| url.indexOf('https://youtube.com') > -1
			|| url.indexOf('https://www.youtube.com') > -1
			|| url.indexOf('http://www.youtube.com') > -1
			|| url.indexOf('http://youtu.be') > -1
			){
			return true;
		} else {
			return false;
		}
	},
	isImageUrl : function(url){
		var imgRegex = /((?:https?):\/\/\S*\.(?:gif|jpg|jpeg|tiff|png|svg|webp))/gi;
		return imgRegex.test(url);
	},
};
