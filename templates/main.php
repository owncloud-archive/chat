<?php
// First load dependencies
vendor_script('chat', array(
	'angular/angular.min',
	'angular-enhance-text/build/angular-enhance-text.min',
	'angular-resource/angular-resource.min',
	'angular-sanitize/angular-sanitize.min',
	'jquery-autosize/jquery.autosize.min',
	'moment/min/moment.min',
	'rangyinputs-jquery-src/index',
	'strophe/strophe.min',
));

if (defined('DEBUG') && DEBUG) {
	vendor_script('chat','angular-mocks/angular-mocks');
}

// Next load source
\OCP\Util::addStyle('chat', 'main.min');
\OCP\Util::addScript('chat', 'main.min');


?>

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