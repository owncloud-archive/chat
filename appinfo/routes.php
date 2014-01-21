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

use OCA\Chat\Core\App;

use OCA\Chat\DependencyInjection\DIContainer;

$this->create('chat_index', '/')->get()->action(
	function($params){
		App::main('PageController', 'index', new DIContainer('chat'), $params);
	}
);

$this->create('chat_api', '/api')->action(
	function($params){
		App::main('ApiController', 'route', new DIContainer('chat'));
	}
);

