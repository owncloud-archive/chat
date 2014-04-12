<?php

namespace OCA\Chat\App;

use OCA\Chat\Core\API;
use OCA\Chat\Controller\AppController;
use OCA\Chat\Controller\OCH\ApiController;
use OCP\AppFramework\App;
use OCA\Chat\Core\AppApi;

/*// to prevent clashes with installed app framework versions
if(!class_exists('\SimplePie')) {
	require_once __DIR__ . '/../3rdparty/simplepie/autoloader.php';
}*/


class Chat extends App {

	public function __construct(array $urlParams=array()){
		parent::__construct('chat', $urlParams);

		$container = $this->getContainer();


		/**
		 * Controllers
		 */
		$container->registerService('AppController', function($c) {
		    return new AppController(
			$c->query('API'), 
			$c->query('Request'),
			$c
		    );
		});
		
		$container->registerService('ApiController', function($c) {
		    return new ApiController(
			$c->query('API'), 
			$c->query('Request'),
			$c
		    );
		});


		/**
		 * Utility
		 */
		$container->registerService('API', function($c){
		    return new API(
			$c->query('AppName')
		    );
		});

		$container->registerService('AppApi', function($c){
		    return new AppApi(
	                $c
		    );
		});
	}
}

