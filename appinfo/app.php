<?php
/**
* ownCloud - Chat app
*
* @author Tobia De Koninck (LEDfan)
* @copyright 2013 Tobia De Koninck tobia@ledfan.be
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace OCA\Chat;

use \OCA\Chat\DependencyInjection\DIContainer;

\OC::$server->getNavigationManager()->add(array(	'id' => 'chat',
    'order' => 10,
    'href' => \OCP\Util::linkToRoute('chat_index'),
    'icon' => \OCP\Util::imagePath('chat', 'chat.png'),
    'name' => \OCP\Util::getL10n('chat')->t('Chat')
));

\OCP\App::registerAdmin('chat', 'admin');

$DIContainer = new DIContainer('chat');
$appApi = $DIContainer['AppApi'];
$appApi->registerBackend('ownCloud Handle', 'och', 'x-owncloud-handle' , 'true');
$appApi->registerBackend('Email', 'email','email' , 'true');
