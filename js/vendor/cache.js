var Cache = (function () {
	var cache = {};

	function time(){
		return parseInt(new Date().getTime() / 1000);
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
					return JSON.parse(value);
				}
			};
		} else {
			cache.storage = {
				set : function(key, value){
					this[key] = value;
				},
				get : function(key){
					return this[key];
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
				delete cache.storage['cache' + key];
				return undefined;
			} else {
				return value.value;
			}
		} else {
			return undefined;
		}

	};

	if(!cache.initialized){
		init();
	}

	return cache;
}());