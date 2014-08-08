<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat;

use \OCA\Chat\App\Chat;
use \OCA\Chat\OCH\Db\User;

\OC::$server->getNavigationManager()->add(array(	'id' => 'chat',
	'order' => 10,
	'href' => \OCP\Util::linkToRoute('chat.app.index'),
	'icon' => \OCP\Util::imagePath('chat', 'chat.png'),
	'name' => \OCP\Util::getL10n('chat')->t('Chat')
));

\OCP\App::registerAdmin('chat', 'admin');
$app = new Chat();
$container = $app->getContainer();
$appApi = $container['AppApi'];
$appApi->registerBackend('ownCloud Handle', 'och', 'x-owncloud-handle' , 1);