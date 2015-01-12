<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
namespace OCA\Chat;

use \OCA\Chat\App\Chat;

include(__DIR__ . '/../lib/compat.php');


\OC::$server->getNavigationManager()->add(array(
	'id' => 'chat',
	'order' => 10,
	'href' => \OCP\Util::linkToRoute('chat.app.index'),
	'icon' => \OCP\Util::imagePath('chat', 'chat.png'),
	'name' => \OCP\Util::getL10n('chat')->t('Chat')
));

$chat = new Chat();
$c = $chat->getContainer();
$och = $c['OCH'];
$chat->registerBackend($och);
$xmpp = $c['XMPP'];
$chat->registerBackend($xmpp);
// Disable the XMPP backend by default when there is no entry in the DB which enables it
// you can manually enable it (https://github.com/owncloud/chat/wiki/FAQ#enabling-a-backend)
$enabled =  \OCP\Config::getAppValue('chat', 'backend_xmpp_enabled');
if ($enabled === null){
	\OCP\Config::setAppValue('chat', 'backend_xmpp_enabled', false);
}

\OCP\App::registerAdmin('chat', 'lib/admin');

// When the "Integrated View" is loaded, include the CSS and JS code:
if ($chat->viewType === Chat::INTEGRATED) {
	if (\OCP\User::isLoggedIn()) {
		vendor_script('chat', 'all.min');
		vendor_style('chat', array(
			'emojione/assets/sprites/emojione.sprites',
			'emojione/assets/css/emojione.min',
		));
		script('chat', 'integrated.min');
		style('chat', 'integrated/main.min');
		if (defined('DEBUG') && DEBUG) {
			vendor_script('chat', 'angular-mocks/angular-mocks');
		}
	}
}