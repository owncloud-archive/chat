<div ng-if="(backends | count) === 0" id="no-backends">
	<?php p($l->t('There are no Chat Backends enabled.')); ?><br />
	<?php p($l->t('In order to chat please enable at least one Chat backend.')); ?><br />
	<?php p($l->t('With the \'OCH\' backend you can chat with other ownCloud users. It works without configuration.')); ?><br />
	<?php p($l->t('It can be enabled by running the following command in the root of your owncloud installation:')); ?><br />
	'./occ chat-backend:enable och'
</div>
