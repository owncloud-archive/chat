<?php
//broken
namespace OCA\Chat\Commands;

use \OCA\Chat\Core\API;
use \OCA\Chat\ChatAPI;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Chat\Db\DoesNotExistException;
use \OCA\Chat\Db\PushMessageMapper;
use \OCA\Chat\Db\PushMessage;

class GetPushMessage extends ChatAPI {
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
