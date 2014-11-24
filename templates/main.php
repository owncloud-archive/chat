<?php
// Fist load all style sheets
\OCP\Util::addStyle('chat', 'main.min');

// Second load all dependencies
\OCP\Util::addScript('chat', 'vendor/angular');
\OCP\Util::addScript('chat', 'vendor/angular-sanitize');
\OCP\Util::addScript('chat', 'vendor/applycontactavatar');
\OCP\Util::addScript('chat', 'vendor/angular-enhance-text');
\OCP\Util::addScript('chat', 'vendor/rangyinputs');
\OCP\Util::addScript('chat', 'vendor/jquery-autosize');
\OCP\Util::addScript('chat', 'vendor/cache');
\OCP\Util::addScript('chat', 'vendor/time');
\OCP\Util::addScript('chat', 'vendor/moment');
\OCP\Util::addScript('chat', 'vendor/strophe');

\OCP\Util::addScript('chat', 'main.min');

if (defined('DEBUG') && DEBUG) {
	\OCP\Util::addScript('chat', 'src/bower_components/angular-mocks/angular-mocks');
}

$version = \OCP\Config::getAppValue('chat', 'installed_version');
if (version_compare($version, '0.2.0.0', '<=')) {
	\OCP\Util::addScript('chat', 'clear');
}

?>
<!--<div ng-app="chat" ng-controller="ConvController"  id="app">-->
<!--</div>-->
<div
	ng-click="view.hide('invite', $event, ['invite-no-hide', 'invite-button']);view.hide('files', $event, ['files-no-hide', 'file-element', 'files-button']);view.hide('emojiContainer', $event, ['emoji-no-hide']);"
	ng-controller="ConvController"
	ng-app="chat"
	id="app"
>
	<div style="display:none;" id="initvar">
		<?php echo $_['initvar']?>
	</div>
	<div id="app-navigation" >
		<?php print_unescaped($this->inc('part.app-navigation')) ?>
	</div>
	<div id="app-content">
		<?php print_unescaped($this->inc('part.chat')) ?>
		<?php print_unescaped($this->inc('part.no-users')) ?>
		<?php print_unescaped($this->inc('part.no-backends')) ?>
	</div>
	<?php print_unescaped($this->inc('part.invite')) ?>
	<?php print_unescaped($this->inc('part.files')) ?>
	<?php print_unescaped($this->inc('part.xmpp')) ?>
	<div id="translations">
		<span id="translations-attached">
			<?php p($l->t('{displayname} attached {path} to this conversation')); ?>
		</span>
        <span id="translations-removed">
			<?php p($l->t('{displayname} removed {path} from this conversation')); ?>
		</span>
	</div>
</div>