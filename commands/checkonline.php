<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;


class CheckOnline extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
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
