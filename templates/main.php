<?php
// Fist load all style sheets
\OCP\Util::addStyle('chat', 'main');

// Second load all dependencies
\OCP\Util::addScript('chat', 'vendor/angular/angular.min');
\OCP\Util::addScript('chat', 'vendor/angular/angular-sanitize');
\OCP\Util::addScript('chat', 'vendor/applycontactavatar');
\OCP\Util::addScript('chat', 'vendor/online');
\OCP\Util::addScript('chat', 'vendor/angular-enhance-text.min');
\OCP\Util::addScript('chat', 'vendor/rangyinputs');
\OCP\Util::addScript('chat', 'vendor/jquery.autosize.min');
\OCP\Util::addScript('chat', 'vendor/cache');



// Third load the Chat object definition
\OCP\Util::addScript('chat', 'chat');

// Fourth load all Chat sub objects
\OCP\Util::addScript('chat', 'app/chat.app');
\OCP\Util::addScript('chat', 'app/chat.app.view');
\OCP\Util::addScript('chat', 'app/chat.app.util');


/***************************/
// Here all backend files will be loaded
\OCP\Util::addScript('chat', 'och/chat.och');
\OCP\Util::addScript('chat', 'och/chat.och.on');
\OCP\Util::addScript('chat', 'och/chat.och.util');
\OCP\Util::addScript('chat', 'och/chat.och.api');
/***************************/

// Fifth load all angular files
\OCP\Util::addScript('chat', 'app/app');
\OCP\Util::addScript('chat', 'app/controllers/convcontroller');

// At last load the main handlers file, this will boot up the Chat app
\OCP\Util::addScript('chat', 'handlers');

?>
<div ng-click="view.hide('settings', $event, 'backend')" ng-controller="ConvController" ng-app="chat" id="app">
	<div style="display:none;" id="initvar">
		<?php echo $_['initvar']?>
	</div>
	<div id="app-navigation" >
		<?php print_unescaped($this->inc('part.app-navigation')) ?>
	</div>
	<div id="app-content">
		<?php print_unescaped($this->inc('part.newconv')) ?>
		<?php print_unescaped($this->inc('part.invite')) ?>
		<?php print_unescaped($this->inc('part.chat')) ?>
	</div>
</div>