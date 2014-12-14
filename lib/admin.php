<?php

namespace OCA\Chat\Admin;

use OCA\Chat\App\Chat;

$app = new Chat();
$container = $app->getContainer();
$response = $container->query('AdminController')->index();
return $response->render();