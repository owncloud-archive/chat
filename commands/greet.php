<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;


class Greet extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}

	public function setCommandData(array $commandData){
		$this->commandData = $commandData;
	}
	
	public function execute(){	

		$commandData = $this->getCommandData();
		\OCP\Util::writeLog('chat', $commandData, \OCP\Util::ERROR);

		$userOnline = new UserOnline();
		$userOnline->setUser($commandData['user']);
		$userOnline->setSessionId($commandData['session_id']);
		$userOnline->setLastOnline($commandData['timestamp']);
		$mapper = new UserOnlineMapper($this->api);
		$mapper->insert($userOnline);   		
		
		return;
	}	

}
