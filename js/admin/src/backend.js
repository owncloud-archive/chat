$(function () {
	$('.backend-checkbox').change(function () {
		$backend = $(this);
		if($backend.attr('checked') === 'checked'){
			enableBackend($backend.attr('name'));
		} else {
			disableBackend($backend.attr('name'));
		}
	});

	function enableBackend(backend){
		$.post(OC.generateUrl('/apps/chat/backend/enable'), {id: backend}, function (data) {
			if (data.status === 'success'){
				OC.Notification.show('Backend ' + backend + ' enabled');
			} else {
				OC.Notification.show('Backend ' + backend + ' can\'t be enabled');
			}
			setTimeout(function () {
				OC.Notification.hide();
			}, 5000);
		});
	}
	function disableBackend(backend){
		$.post(OC.generateUrl('/apps/chat/backend/disable'), {id: backend}, function (data) {
			if (data.status === 'success'){
				OC.Notification.show('Backend ' + backend + ' disabled');
			} else {
				OC.Notification.show('Backend ' + backend + ' can\'t be disabled');
			}
			setTimeout(function () {
				OC.Notification.hide();
			}, 5000);
		});
	}
});
