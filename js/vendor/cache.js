var Cache = (function () {
	var cache = {};

	function time(){
		return parseInt(new Date().getTime() / 1000);
	}

	cache.storage = {};
	cache.set = function (key, value, expire) {
		key = 'cache' + key;
		var date = time() + expire;
		cache.storage[key] = {
			"key" : key,
			"value": value,
			"expire" : date
		};
	};

	cache.get = function (key) {
		var value = cache.storage['cache' + key];
		if(value !== undefined){
			if((value.expire - time()) <=0 ) {
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
	return cache;
}());