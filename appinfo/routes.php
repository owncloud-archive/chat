<?php
/**
* ownCloud - Chat app
*
* @author Tobia De Koninck (LEDfan)
* @copyright 2013 Tobia De Koninck tobia@ledfan.be
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace OCA\Chat;

use \OCA\Chat\App\Chat;

// Note that action can't be used as keyword

$application = new Chat();
$application->registerRoutes($this, array(
	'routes' => array(
		array(  'name' => 'app#index',
			'url' => '/',
			'verb' => 'GET'),
		array(  'name' => 'api#route',
			'url' => '/och/api',
			'verb' => 'post'
		),
		array(  'name' => 'app#backend',
			'url' => '/backend/{do}/{backend}/{id}',
			'verb' => 'post'
		),
		array(  'name' => 'app#contacts',
			'url' => '/contacts',
			'verb' => 'post' // TODO GET
		)
	)
));