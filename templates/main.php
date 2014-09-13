<?php
$this->inc('scripts');

$version = \OCP\Config::getAppValue('chat', 'installed_version');
if (version_compare($version, '0.2.0.0', '<=')) {
	\OCP\Util::addScript('chat', 'clear');
}

?>
<div ng-click="view.hide('invite', $event, ['invite-no-hide', 'invite-button'])" ng-controller="ConvController" ng-app="chat" id="app">
	<div style="display:none;" id="initvar">
		<?php echo $_['initvar']?>
	</div>
	<div id="app-navigation" >
		<?php print_unescaped($this->inc('part.app-navigation')) ?>
	</div>
	<div id="app-content">
		<?php print_unescaped($this->inc('part.chat')) ?>
		<?php print_unescaped($this->inc('part.no-users')) ?>
	</div>
	<?php print_unescaped($this->inc('part.invite')) ?>
</div>