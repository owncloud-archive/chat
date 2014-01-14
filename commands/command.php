<?php

namespace OCA\Chat\Commands;
use \OCA\AppFramework\Core\API;

abstract class Command {

	public $api;
	protected $commandData;
	
	public function __construct(API $api){
		$this->api = $api;
	}
	
	abstract function setCommandData(array $commandData);

	public function getCommandData(){
		return $this->commandData;
	}

	
	abstract public function execute();	

}
