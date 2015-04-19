angular.module('chat').factory('time', [function() {
    return {
        now: function () {
			return Math.floor(Date.now() / 1000);
        },
        timestampToObject: function (timestamp) {
            var date = new Date(timestamp * 1000);
            var hours = date.getHours();
            var minutes = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
            var seconds = date.getSeconds();
            return	{hours : date.getHours(), minutes : (date.getMinutes() < 10 ? '0' : '') + date.getMinutes(), seconds : date.getSeconds()};
        },
        format: function (timestamp) {
            return moment(timestamp * 1000).format("MMMM D, YYYY h:mm");
        }



    };
}]);