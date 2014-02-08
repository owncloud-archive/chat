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

use \OCA\Chat\Core\API;

\OC::$CLASSPATH['OCA\Chat\ChatAPI'] = 'chat/lib/chatapi.php';

\OC::$CLASSPATH['OCA\Chat\OCH\Commands\Greet'] = 'chat/lib/och/commands/greet.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Commands\CheckOnline'] = 'chat/lib/och/commands/checkonline.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Commands\Invite'] = 'chat/lib/och/commands/invite.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Commands\Join'] = 'chat/lib/och/commands/join.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Commands\Leave'] = 'chat/lib/och/commands/leave.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Commands\Online'] = 'chat/lib/och/commands/online.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Commands\Quit'] = 'chat/lib/och/commands/quit.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Commands\SendChatMsg'] = 'chat/lib/och/commands/sendchatmsg.php';

\OC::$CLASSPATH['OCA\Chat\OCH\Respones\Success'] = 'chat/lib/och/responses/success.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Respones\Error'] = 'chat/lib/och/responses/error.php';

\OC::$CLASSPATH['OCA\Chat\OCH\Exceptions\CommandDataInvalid'] = 'chat/lib/och/exceptions/commanddatainvalid.php';

\OC::$CLASSPATH['OCA\Chat\OCH\Push\Get'] = 'chat/lib/och/push/get.php';
\OC::$CLASSPATH['OCA\Chat\OCH\Push\Delete'] = 'chat/lib/och/push/delete.php';

$api = new API('chat');

$api->addNavigationEntry(array(
	'id' => 'chat',
	'order' => 10,
	'href' => $api->linkToRoute('chat_index'),
	'icon' => $api->imagePath('chat.png'),
	'name' => $api->getTrans()->t('Chat')
));