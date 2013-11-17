<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Appframework\Db\DoesNotExistException;
use \OCA\Chat\Db\PushMessageMapper;
use \OCA\Chat\Db\PushMessage;

class GetPushMessage extends Command {
	private $pushMessage;
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}
	
	public function execute(){
		try {
			$mapper = new PushMessageMapper($this->api); // inject API class for db access
			$this->pushMessages = $mapper->findBysSessionId($this->params('sessionID'));					
		} catch(DoesNotExistException $e){
			sleep(1);
			$this->execute();
		}
		return $this->pushMessages;	
	}
			

}
