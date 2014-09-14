var Time = (function () {
	var time = {};

	time.now = function (key) {
		return parseInt(new Date().getTime() / 1000);
	};

	return time;
}());