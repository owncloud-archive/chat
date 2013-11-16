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
	
	public function execute(){	
		if(in_array($this->params('user'), \OCP\User::getUsers())){   		
    		$userOnline = new UserOnline();
    		$userOnline->setUser($this->params('user'));
			$userOnline->setSessionId($this->params('sessionID'));
    		$mapper = new UserOnlineMapper($this->api);
    		$mapper->insert($userOnline);   		
    		
			return true;
    	} else {
    		throw new NoOcUserException('NO-OC-USER');
    	}
	}	

}
