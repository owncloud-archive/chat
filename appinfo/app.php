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

use \OCA\AppFramework\Core\API;


if(\OCP\App::isEnabled('appframework')){

	$api = new API('chat');

	$api->addNavigationEntry(array(

	'id' => $api->getAppName(),

	'order' => 10,
	
	'href' => $api->linkToRoute('chat_index'),
	
	'icon' => $api->imagePath('chat.png'),
	
	'name' => $api->getTrans()->t('Chat')
	
	));


} else {
	$msg = 'Can not enable the Chat app because the App Framework App is disabled';
	\OCP\Util::writeLog('chat', $msg, \OCP\Util::ERROR);
}