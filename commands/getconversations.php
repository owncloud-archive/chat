<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;

class GetConversations extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}
	
	public function execute(){
		$userMapper = new UserMapper($this->api);
		$conversations = $userMapper->findByUser($this->params('user'));
		$response = array();
		foreach($conversations as $conversation){
			array_push($response, $conversation->getConversationId());
		}
		return $response;
	}	

}
