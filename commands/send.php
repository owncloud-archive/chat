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

class Send extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}
	
	public function execute(){
   		$userMapper = new UserMapper($this->api);
	   	$users = $userMapper->findByConversation($this->params('conversationID'));
		$command = json_encode(array('type' => 'send', 'param' => array('user' => $this->params('user'), 'conversationID' => $this->params('conversationID'), 'msg' => $this->params('msg'))));	
		$sender = $this->params('user'); // copy the params('user') to a variable so it won't be called many times in a large conversation
		$PushMessageMapper = new PushMessageMapper($this->api);
		
		foreach($users as $receiver){
			$pushMessage = new PushMessage();
			$pushMessage->setSender($sender);
			$pushMessage->setReceiver($receiver->getUser());
			$pushMessage->setReceiverSessionId($receiver->getSessionId());
			$pushMessage->setCommand($command);
			$PushMessageMapper->insert($pushMessage);	
		}
		return true;
	}	

}
