<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Chat\Db\Conversation;
use \OCA\Chat\Db\ConversationMapper;

class Leave extends Command {
	
	public function __construct(API $api, $params){	
		parent::__construct($api, $params);
	}
	
	public function execute(){
		$userMapper = new UserMapper($this->api);
		$userMapper->deleteBySessionId($this->params('conversationID'), $this->params('sessionID'));
		
		// Check if there are still users in this conversation
		// If there are no users in the conversation, delete the conversation 
		
		// fetch users in conversation by conversationid
		$users = $userMapper->findByConversation($this->params('conversationID'));
		// TODO check for multiple sessions by the same user
		if(count($users) <= 1){
			// there are no users in the conversatio -> conversation can be deleted
			$conversationMapper = new ConversationMapper($this->api);
			$conversationMapper->deleteConversation($this->params('conversationID'));
		}		
		
	}	

}



		
	