<?php
namespace OCA\Chat;

use \OCA\Chat\App\Chat;

// Reforce creating of backends
$query = \OCP\DB::prepare('DELETE FROM `*PREFIX*chat_backends`');
$query->execute(array());

$chat = new Chat();
$chat->registerBackend('ownCloud Handle', 'och', 'x-owncloud-handle' , true);
