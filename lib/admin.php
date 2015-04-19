<?php

namespace OCA\Chat\Admin;

use OCA\Chat\App\Container;

$container = new Container();
$response = $container->query('AdminController')->index();
return $response->render();