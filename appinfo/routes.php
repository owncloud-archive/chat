<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat;

use \OCA\Chat\App\Chat;

// Note that action can't be used as keyword
$application = new Chat();
$application->registerRoutes($this, array(
	'routes' => array(
		array(
			'name' => 'app#index',
			'url' => '/',
			'verb' => 'GET'),
		array(
			'name' => 'api#route',
			'url' => '/och/api',
			'verb' => 'post'
		),
		array(
			'name' => 'app#backend',
			'url' => '/backend/{do}/{backend}/{id}',
			'verb' => 'post'
		),
		array(
			'name' => 'app#contacts',
			'url' => '/contacts',
			'verb' => 'get'
		)
	)
));