<?php

$app = new OCA\Chat\App\Chat;
$application->add(new OCA\Chat\Command\EnableBackend($app));
$application->add(new OCA\Chat\Command\DisableBackend($app));