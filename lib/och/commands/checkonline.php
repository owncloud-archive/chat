<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnline;
use \OCA\Chat\OCH\Db\UserOnlineMapper;


class CheckOnline extends ChatAPI {
	
	public function __construct(API $api){
		parent::__construct($api);
	}

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}
	
	public function execute(){	
    	$mapper = new UserOnlineMapper($this->api);
    	$users = $mapper->getAll();
		foreach($users as $user){
			if((time() - $user->getLastOnline()) > 60){
				$mapper->deleteBySessionId($user->getSessionId());	
			}
		}

	}	

}
