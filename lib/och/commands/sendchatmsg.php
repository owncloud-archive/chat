<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnline;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Db\User;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\Conversation;
use \OCA\Chat\OCH\Db\ConversationMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;


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
