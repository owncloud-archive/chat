<?php

$app = new OCA\Chat\App\Chat;
$application->add(new OCA\Chat\Command\EnableBackend($app->c['BackendManager']));
$application->add(new OCA\Chat\Command\DisableBackend($app->c['BackendManager']));