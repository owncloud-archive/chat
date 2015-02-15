<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat;

use \OCA\Chat\App\Chat;
use \OCP\Util;
use \OCP\App;

$chat = new Chat();
$chat->query('OCP\INavigationManager')->add(array(
	'id' => 'chat',
	'order' => 10,
	'href' => Util::linkToRoute('chat.app.index'),
	'icon' => Util::imagePath('chat', 'chat.png'),
	'name' => Util::getL10n('chat')->t('Chat')
));
$chat->registerBackend($chat->query('OCH'));
$chat->registerBackend($chat->query('XMPP'));

// Disable the XMPP backend by default when there is no entry in the DB which enables it
// you can manually enable it (https://github.com/owncloud/chat/wiki/FAQ#enabling-a-backend)
$enabled =  $chat->query('OCP\IConfig')->getAppValue('chat', 'backend_xmpp_enabled');
if ($enabled === null){
	$chat->query('OCP\IConfig')->setAppValue('chat', 'backend_xmpp_enabled', false);
}

App::registerAdmin('chat', 'lib/admin');