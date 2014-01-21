<?php

namespace OCA\Chat\Commands;

use OCA\Chat\ChatAPI;

use \OCA\Chat\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Chat\Db\Conversation;
use \OCA\Chat\Db\ConversationMapper;
use \OCA\Chat\Db\PushMessage;
use \OCA\Chat\Db\PushMessageMapper;

use \OCA\Chat\Exceptions\RequestDataInvalid;


class SendChatMsg extends ChatAPI {
	
	public function __construct(API $api){
		parent::__construct($api);
	}

	public function setRequestData(array $requestData){
		if(empty($requestData['chat_msg'])){
			throw new RequestDataInvalid("CHAT-MSG-MUST-BE-PROVIDED");
		}
		if(empty($requestData['timestamp'])){
			throw new RequestDataInvalid("TIMESTAMP-MUST-BE-PROVIDED");
		}
		$this->requestData = $requestData;
	}

	public function execute(){
   		$userMapper = new UserMapper($this->api);
	   	$users = $userMapper->findByConversation($this->requestData['conv_id']);

		$command = json_encode(array(
			'type' => 'send_chat_msg',
			'data' => array(
				'user' => $this->requestData['user'], 
				'conv_id' => $this->requestData['conv_id'],
				'timestamp' => $this->requestData['timestamp'], 
				'chat_msg' => $this->requestData['chat_msg']
				)
			)
		);	
				
		$sender = $this->requestData['user']; 
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
		return;
	}	

}
