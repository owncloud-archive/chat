<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Chat\Commands\Leave;

class Quit extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}
	
	public function execute(){
			
		// First delete the sessionid from the online user table
		$userOnlineMapper = new UserOnlineMapper($this->api);
		$userOnlineMapper->deleteBySessionId($this->params('sessionID'));
		
		// fetch all conversations where this sessionid joined
		
		$userMapper = new UserMapper($this->api);
		$conversations = $userMapper->findBySessionId($this->params('sessionID'));
		
		foreach($conversations as $conversation){			
			// For each conversation, create a leave command and execute it
			$leave = new Leave($this->api, array('conversationID' => $conversation->getConversationId(), 'sessionID' => $this->params('sessionID')));
			$leave->execute();		
			// Left conversation 
		}
	}	

}



		
	