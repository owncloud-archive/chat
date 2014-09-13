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
\OCP\Util::addScript('chat', 'vendor/time');


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
\OCP\Util::addScript('chat', 'app/directives/avatar');
\OCP\Util::addScript('chat', 'app/directives/ngenter');
\OCP\Util::addScript('chat', 'app/directives/tipsy');
\OCP\Util::addScript('chat', 'app/directives/displayname');
\OCP\Util::addScript('chat', 'app/directives/autoheight');
\OCP\Util::addScript('chat', 'app/directives/invitemobile');
\OCP\Util::addScript('chat', 'app/filters/orderobjectby');
\OCP\Util::addScript('chat', 'app/filters/user');
\OCP\Util::addScript('chat', 'app/filters/filterusersinconv');

// At last load the main handlers file, this will boot up the Chat app
\OCP\Util::addScript('chat', 'handlers');
