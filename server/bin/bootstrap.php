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
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
/*
 * @Brief Create a $user object so we can insert it into the Chat Class
 * @TODO 
 */
include dirname(__DIR__).'/../../../lib/base.php';
OC::autoLoad('OCP\User');
$user = new OC_user;

/*
 * @Brief Create a pid file so there won't be 2 servers running at the same time
 * @TODO develop this feature
 */
$pid = getmypid();
echo $pid;


/*
 * @Brief boot the Chat Server 
 */
require dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__).'/src/chat.php';
$server = IoServer::factory(
	new WsServer(
		new \OCA\Chat\Chat($user)
	)
	, 8080
);

$server->run();
