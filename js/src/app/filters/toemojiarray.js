angular.module('chat').filter('toEmojiArray', function() {
	return function(items) {
		var array = [];
		for (var key in items){
			if (items.hasOwnProperty(key)){
				var item = items[key];
				if (item.length === 2){
					array.push({key: key, value: item[1]});
				} else {
					array.push({key: key, value: item[0]});
				}
			}
		}
		return array;
	};
});
