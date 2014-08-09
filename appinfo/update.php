<?php
namespace OCA\Chat;

use \OCA\Chat\App\Chat;

$version = \OCP\Config::getAppValue('chat', 'installed_version');
//$Chat = new Chat();
//$container = $Chat->getContainer();
//$db = $container->query('ServerContainer')->getDb();

if (version_compare($version, '0.2.0.0', '<=')) {
	$q = \OCP\DB::prepare("DELETE FROM oc_chat_backends");
	$q->execute(array());
}

//$msg = 'Updated Chat app from version ' . $version . ' to ' .\OCP\App::getAppVersion('chat');
//\OCP\Util::writeLog('chat', $msg, \OCP\Util::ERROR);