<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat;

use \OCA\Chat\App\Chat;
use \OCA\Chat\App\Container;
use \OCP\Util;
use \OCP\App;

$container = new Container();

$container->query('OCP\INavigationManager')->add(function(){
	return array(
		'id' => 'chat',
		'order' => 10,
		'href' => Util::linkToRoute('chat.app.index'),
		'icon' => Util::imagePath('chat', 'chat.png'),
		'name' => Util::getL10n('chat')->t('Chat')
	);
});

$container->query('Chat')->registerBackend($container->query('OCH'));
$container->query('Chat')->registerBackend($container->query('XMPP'));

App::registerAdmin('chat', 'lib/admin');

// When the "Integrated View" is loaded, include the CSS and JS code:
if ($container->query('Chat')->viewType === Chat::INTEGRATED) {
	if (\OCP\User::isLoggedIn()) {
		Util::addStyle('chat', '../vendor/emojione/assets/sprites/emojione.sprites');
		Util::addStyle('chat', '../vendor/emojione/assets/css/emojione.min');
		Util::addScript('chat', '../vendor/all.min');
		Util::addScript('chat', 'integrated.min');
		Util::addStyle('chat', 'integrated.min');

		if (defined('DEBUG') && DEBUG) {
			Util::addScript('chat', '../vendor/angular-mocks/angular-mocks');
		}
	}
}
$container->query('Chat')->registerExceptionHandler();
