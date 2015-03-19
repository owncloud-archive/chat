<?php

$app = new OCA\Chat\App\Chat;
$application->add(new OCA\Chat\Command\EnableBackend($app->query('BackendManager')));
$application->add(new OCA\Chat\Command\DisableBackend($app->query('BackendManager')));
$application->add(new OCA\Chat\Command\UnInstall($app->query('OCP\App\IAppManager'), $app->query('OCP\IDb')));