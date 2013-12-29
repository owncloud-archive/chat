<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Chat\Db\ConversationMapper;
use \OCA\Chat\Db\Conversation;

class GetConversations extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}
	
	public function execute(){
		$userMapper = new UserMapper($this->api);
		$conversationsDB = $userMapper->findByUser($this->params('user'));
		$conversations = array();
		foreach($conversationsDB as $conversationDB){
   			$conversationMapper = new ConversationMapper($this->api); 
			$conversation = array("conversationID" => $conversationDB->getConversationId());
			array_push($conversations, $conversation);
		}
		return $conversations;
	}	

}
