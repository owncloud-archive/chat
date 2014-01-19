<?php

namespace OCA\Chat\Commands;

use \OCA\Chat\ChatAPI;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;


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
