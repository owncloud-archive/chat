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

	
	\OC::$CLASSPATH['OCA\Chat\Commands\Greet'] = 'chat/lib/commands/greet.php';
	\OC::$CLASSPATH['OCA\Chat\Commands\CheckOnline'] = 'chat/lib/commands/checkonline.php';
	\OC::$CLASSPATH['OCA\Chat\Commands\Command'] = 'chat/lib/commands/command.php';
	\OC::$CLASSPATH['OCA\Chat\Commands\Invite'] = 'chat/lib/commands/invite.php';
	\OC::$CLASSPATH['OCA\Chat\Commands\Join'] = 'chat/lib/commands/join.php';
	\OC::$CLASSPATH['OCA\Chat\Commands\Leave'] = 'chat/lib/commands/leave.php';
	\OC::$CLASSPATH['OCA\Chat\Commands\Online'] = 'chat/lib/commands/online.php';
	\OC::$CLASSPATH['OCA\Chat\Commands\Quit'] = 'chat/lib/commands/Quit.php';
	\OC::$CLASSPATH['OCA\Chat\Commands\Send'] = 'chat/lib/commands/Send.php';

	\OC::$CLASSPATH['OCA\Chat\Respones\Success'] = 'chat/lib/responses/success.php';
	\OC::$CLASSPATH['OCA\Chat\Respones\Error'] = 'chat/lib/responses/error.php';

	\OC::$CLASSPATH['OCA\Chat\Exceptions\CommandDataInvalid'] = 'chat/lib/exceptions/commanddatainvalid.php';

 


 } else {
	$msg = 'Can not enable the Chat app because the App Framework App is disabled';
	\OCP\Util::writeLog('chat', $msg, \OCP\Util::ERROR);
}
