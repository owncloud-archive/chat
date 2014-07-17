var Cache = (function () {
	var cache = {};

	cache.storage = {};
	cache.set = function (key, value, expire) {
		key = 'cache' + key;
		var date = new Date().getTime() / 1000;
		date = parseInt(date) + expire;
		cache.storage[key] = {
			"key" : key,
			"value": value,
			"expire" : date
		};
	};

	cache.get = function (key) {
		return cache.storage[key];
	};
	return cache;
}());