<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Chat\Db\Conversation;
use \OCA\Chat\Db\ConversationMapper;
use \OCA\Chat\Db\PushMessage;
use \OCA\Chat\Db\PushMessageMapper;

class Join extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}
	
	public function execute(){
		if(in_array($this->params('user'), \OCP\User::getUsers())){ 

	   		$userMapper = new UserMapper($this->api);
	   		$users = $userMapper->findByConversation($this->params('conversationID'));
	   		
	   		if (count($users) === 0){
	   			$conversation = new Conversation();
	   			$conversation->setConversationId($this->params('conversationID'));
	   			$mapper = new ConversationMapper($this->api); 
	   			$mapper->insert($conversation);
	   			 
	   			$user = new User();
	   			$user->setConversationId($this->params('conversationID'));
	   			$user->setUser($this->params('user'));
				$user->setSessionId($this->params('sessionID'));
	   			$userMapper = new UserMapper($this->api);
	   			$userMapper->insert($user);
	   			
	   			return true;
	   		} else { 
	   			$user = new User();
	   			$user->setConversationId($this->params('conversationID'));
	   			$user->setUser($this->params('user'));
				$user->setSessionId($this->params('sessionID'));
	   			$userMapper = new UserMapper($this->api);
	   			$userMapper->insert($user);
				
	   			if (count($users) == 2){
		   			// Send a push message to all users in this conversation to inform about a new user which joined
		   			$command = json_encode(array('type' => 'joined', 'param' => array('user' => $this->params('user'), 'timestamp' => $this->params('timestamp'),  'conversationID' => $this->params('conversationID'))));
		   			$sender = $this->params('user'); // copy the params('user') to a variable so it won't be called many times in a large conversation
		   			$PushMessageMapper = new PushMessageMapper($this->api);
		   			foreach($users as $receiver){
		   				if($receiver->getUser() !== $sender){
		   					$pushMessage = new PushMessage();
		   					$pushMessage->setSender($sender);
		   					$pushMessage->setReceiver($receiver->getUser());
		   					$pushMessage->setReceiverSessionId($receiver->getSessionId());
		   					$pushMessage->setCommand($command);
		   					$PushMessageMapper->insert($pushMessage);
		   				}
		   			}
	   			}
	   			
	   			return true;
	   		} 
	   	} else {
    		throw new NoOcUserException('NO-OC-USER');
	   	}
	}	

}
