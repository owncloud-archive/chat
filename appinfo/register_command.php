<?php

$container = new OCA\Chat\App\Container();
$application->add(new OCA\Chat\Command\EnableBackend($container->query('BackendManager')));
$application->add(new OCA\Chat\Command\DisableBackend($container->query('BackendManager')));
$application->add(new OCA\Chat\Command\UnInstall($container->query('OCP\App\IAppManager'), $container->query('OCP\IDb')));