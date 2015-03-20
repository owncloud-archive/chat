<?php
use \OCA\Chat\App\Container;

$container = new Container();
// Disable the XMPP backend by default when there is no entry in the DB which enables it
// you can manually enable it (https://github.com/owncloud/chat/wiki/FAQ#enabling-a-backend)
$container->query('OCP\IConfig')->setAppValue('chat', 'backend_xmpp_enabled', false);