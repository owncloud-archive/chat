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

use OCA\AppFramework\App;

use OCA\Chat\DependencyInjection\DIContainer;

/**
 * Webinterface
 */
$this->create('chat_index', '/')->get()->action(
	function($params){
		App::main('PageController', 'index', $params, new DIContainer());
	}
);

/**
 * Commands used by the AJAX client
 */

$this->create('command_greet', '/command/greet')->post()->action(
	function($params){
		App::main('CommandController', 'greet', $params, new DIContainer());
	}
);

$this->create('command_join', '/command/join')->post()->action(
	function($params){
		App::main('CommandController', 'join', $params, new DIContainer());
	}
);

$this->create('command_invite', '/command/invite')->post()->action(
		function($params){
			App::main('CommandController', 'invite', $params, new DIContainer());
		}
);

$this->create('command_leave', '/command/leave')->post()->action(
		function($params){
			App::main('CommandController', 'leave', $params, new DIContainer());
		}
);

$this->create('command_getusers', '/command/getusers')->post()->action(
		function($params){
			App::main('CommandController', 'getusers', $params, new DIContainer());
		}
);

$this->create('command_send', '/command/send')->post()->action(
		function($params){
			App::main('CommandController', 'send', $params, new DIContainer());
		}
);

$this->create('command_get_conversations', '/command/getconversations')->post()->action(
		function($params){
			App::main('CommandController', 'getConversations', $params, new DIContainer());
		}
);

/* 
 * Receive and delete push message
 */
$this->create('push_get', '/push/get')->post()->action(
		function($params){
			App::main('PushMessageController', 'get', $params, new DIContainer());
		}
);
 
 $this->create('push_delete', '/push/delete')->post()->action(
		function($params){
			App::main('PushMessageController', 'delete', $params, new DIContainer());
		}
);

