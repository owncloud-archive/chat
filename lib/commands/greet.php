<?php

namespace OCA\Chat\Commands;

use \OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;
use \OCA\Chat\Exceptions\CommandDataInvalid;

class Greet extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}

	/*
	 * @param $commandData['user'] String user id of the client
	 * @param $commandData['session_id'] String session_id of the client
	 * @param $commandData['timestamp'] Int timestamp when the command was send
	*/
	public function setCommandData(array $commandData){
		$this->commandData = $commandData;
	}
	
	public function execute(){	
		$commandData = $this->getCommandData();
		$userOnline = new UserOnline();
		$userOnline->setUser($commandData['user']);
		$userOnline->setSessionId($commandData['session_id']);
		$userOnline->setLastOnline($commandData['timestamp']);
		$mapper = new UserOnlineMapper($this->api);
		$mapper->insert($userOnline);   		
		return;
	}	

}
