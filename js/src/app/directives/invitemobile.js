angular.module('chat').directive('inviteMobile', function () {
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {
			if($(window).width() < 768){
				// we are
				// this means the navigation is hidden
				// we have to position the invite popover below the "add person" button
				element.addClass('invite-container-mobile');
			}
		}
	};
});