<?php

namespace OCA\Chat\Commands;

use \OCA\Chat\ChatAPI;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;
use \OCA\Chat\Exceptions\CommandDataInvalid;

class Greet extends ChatAPI {
	
	public function __construct(API $api){
		parent::__construct($api);
	}

	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['session_id'] String session_id of the client
	 * @param $requestData['timestamp'] Int timestamp when the command was send
	*/
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}
	
	public function execute(){	
		$requestData = $this->getRequestData();
		$userOnline = new UserOnline();
		$userOnline->setUser($requestData['user']);
		$userOnline->setSessionId($requestData['session_id']);
		$userOnline->setLastOnline($requestData['timestamp']);
		$mapper = new UserOnlineMapper($this->api);
		$mapper->insert($userOnline);   		
		return;
	}	

}
