<?php

namespace OCA\Chat\Commands;

use \OCA\Chat\ChatAPI;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;
use \OCA\Chat\Commands\CheckOnline;


class Online extends ChatAPI {
	
	public function __construct(API $api){
		parent::__construct($api);
	}
	

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){	
		$mapper = new UserOnlineMapper($this->api);
		$mapper->updateLastOnline($this->requestData['session_id'], $this->requestData['timestamp']);   		
		
		$checkOnline = new CheckOnline($this->api);
		$checkOnline->execute();
		return;
	}	

}
