<?php
//broken
namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\User;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\DoesNotExistException;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Db\PushMessage;

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
