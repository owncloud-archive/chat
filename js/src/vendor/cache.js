/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
var Cache = (function () {
	var cache = {};

	function time(){
		return Math.floor(Date.now() / 1000);
	}

	function init(){
		// check for LocalStorage
		if(supportLocalstorage){
			cache.storage = {
				set : function(key, value){
					value = JSON.stringify(value);
					localStorage[key] = value;
				},
				get : function(key){
					var value = localStorage[key];
					if(value !== undefined){
						return JSON.parse(value);
					} else {
						return undefined;
					}
				},
				remove : function(key){
					localStorage.removeItem(key);
				}
			};
		} else {
			cache.storage = {
				set : function(key, value){
					this[key] = value;
				},
				get : function(key){
					return this[key];
				},
				remove : function(key){
					delete this[key];
				}
			};
		}
	}

	function supportLocalstorage() {
		try {
			return 'localStorage' in window && window['localStorage'] !== null;
		} catch (e) {
			return false;
		}
	}

	cache.initialized = false;
	cache.set = function (key, value, expire) {
		if(expire === undefined){
			var date = 0;
		} else {
			var date = time() + expire;
		}
		key = 'cache' + key;
		cache.storage.set(key, {
			"key" : key,
			"value": value,
			"expire" : date
		});
	};

	cache.get = function (key) {
		var value = cache.storage.get('cache' + key);
		if(value !== undefined){
			if (value.expire === 0){
				return value.value;
			} else if ((value.expire - time()) <=0 ) {
				// Expired
				cache.storage.remove('cache' + key);
				return undefined;
			} else {
				return value.value;
			}
		} else {
			return undefined;
		}

	};

	cache.day = function(count){
		return 86400 * count;
	};

	cache.hour = function(count){
		return 3600 * count;
	};

	cache.minute = function(count){
		return 60 * count;
	};

	if(!cache.initialized){
		init();
	}

	return cache;
}());