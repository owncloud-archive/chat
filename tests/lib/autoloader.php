<?php

/**
 * ownCloud - Notes app
 *
 * @author Bernhard Posselt
 *
 * @copyright 2013 Bernhard Posselt <dev@bernhard-posselt.com>
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


// to execute without owncloud, we need to create our own classloader
spl_autoload_register(function ($className){
	if (strpos($className, 'OCA\\') === 0) {
	    $path = strtolower(str_replace('\\', '/', substr($className, 3)) . '.php');
	    $relPath = __DIR__ . '/../../..' . $path;
	    if(file_exists($relPath)){
		require_once $relPath;
	    } else {
		list(, $app, $rest) = explode('\\', $className, 3);
		$app = strtolower($app);
		$path = strtolower(str_replace('\\', '/', $rest . '.php'));
		$relPath = __DIR__ . '/../../../' . $app. '/lib/' .$path;
		//echo "\n\n\n\n\n" . $relPath . "\n\n\n\n";
		require_once $relPath;
	    }
	} elseif (strpos($className, 'OCP\\') === 0) {
	    $path = strtolower(str_replace('\\', '/', substr($className, 3)) . '.php');
	    $relPath = __DIR__ . '/../../../../lib/public' . $path;
	    if(file_exists($relPath)){
		require_once $relPath;
	    }
	}
	

});

use \OCP\Util;
new Util();