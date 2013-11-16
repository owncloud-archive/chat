<?php

namespace OCA\Chat\Commands;
use \OCA\AppFramework\Core\API;

abstract class Command {

	public $api;
	protected $params;
	
	public function __construct(API $api, $params){
		
		$this->api = $api;
		$this->params = $params;
		
	}
	
	public function params($key, $default=null){
		
	    return isset($this->params[$key])
	        ? $this->params[$key]
	        : $default;
	}
	
	abstract public function execute();	

}
