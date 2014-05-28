<?php

//class OC {
//	public static $server;
//}

set_include_path(__DIR__ . "/vendor");
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
			require_once $relPath;
		}
	} elseif (strpos($className, 'OCP\\') === 0) {
		$path = strtolower(str_replace('\\', '/', substr($className, 3)) . '.php');
		$relPath = __DIR__ . '/../../../../lib/public' . $path;
		if(file_exists($relPath)){
			require_once $relPath;
		}
	} elseif (strpos($className, 'OC\\') === 0) {
		$path = strtolower(str_replace('\\', '/', substr($className, 2)) . '.php');
		$relPath = __DIR__ . '/../../../../lib/private/' . $path;
		if(file_exists($relPath)){
			require_once $relPath;
		}
	} elseif (strpos($className, 'Pimple\Pimple' === 0)){
		require_once("../vendor/Pimple/Pimple.php");
	}

});
