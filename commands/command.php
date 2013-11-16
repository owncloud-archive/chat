<?php

namespace OCA\Chat\Commands;
use \OCA\AppFramework\Core\API;

abstract class Command {

	public $api;
	private $params;
	
	public function __construct(API $api, $params){
		$this->api = $api;
		$this->params = $params;
		\OCP\Util::writeLog('chat', 'this' . $this->params, \OCP\Util::ERROR);
		\OCP\Util::writeLog('chat', $params, \OCP\Util::ERROR);
	
	}
	
	public function params($key, $default=null){
	    return isset($this->params[$key])
	        ? $this->params[$key]
	        : $default;
	}
	
	abstract public function execute();	

}
