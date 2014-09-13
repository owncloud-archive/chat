Chat.angular.filter('orderObjectBy', function() {
	return function(items, field, reverse) {
		var filtered = [];
		for (var key in items){
			var item = items[key];
			filtered.push(item);
		}
		filtered.sort(function (a, b) {
			return (a[field] > b[field] ? 1 : -1);
		});
		if(reverse) filtered.reverse();
		return filtered;
	};
})