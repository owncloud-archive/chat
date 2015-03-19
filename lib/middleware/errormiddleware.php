<?php

namespace OCA\Chat\Middleware;

use \OCP\AppFramework\Middleware;
use \OCA\Chat\App\Chat;

class ErrorMiddleware extends Middleware {

	/**
	 * @var Chat
	 */
	private $app;

	public function __construct(Chat $app){
		$this->app = $app;
	}


	/**
	 * this replaces "bad words" with "********" in the output
	 */
	public function afterException($controller, $methodName, \Exception $e){
		$this->app->exceptionHandler($e);
	}

}