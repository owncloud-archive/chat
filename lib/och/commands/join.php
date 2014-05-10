<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\User;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use OCA\Chat\OCH\Db\InitConv;

class Join extends ChatAPI {

	public function setRequestData(array $requestData){
		if(empty($requestData['conv_id'])){
			throw new RequestDataInvalid("CONV-ID-MUST-BE-PROVIDED");
		}
		$this->requestData = $requestData;
	}

	public function execute(){
		$user = new User();
		$user->setConversationId($this->requestData['conv_id']);
		$user->setUser($this->requestData['user']['backends']['och']['value']);
		$user->setSessionId($this->requestData['session_id']);
		$userMapper = $this->app['UserMapper'];
		$userMapper->insertUnique($user);

		// mark this conv as a init conv => the conv is auto joined on refresh
		$initConv = new InitConv();
		$initConv->setConvId($this->requestData['conv_id']);
		$initConv->setUser($this->requestData['user']['backends']['och']['value']);
		$initConvMapper = $this->app['InitConvMapper'];
		$initConvMapper->insertUnique($initConv);
		
		/*$users = $userMapper->findByConversation($this->requestData['conv_id']);
		if (count($users) > 2){
			// Send a push message to all users in this conversation to inform about a new user which joined
			$command = json_encode(array(
				"type" => 'joined',
				"data" => array(
					"user" => $this->requestData['user']['backends']['och']['value'],
					"timestamp" => $this->requestData['timestamp'],
					"conv_id" => $this->requestData['conv_id']
				)
			));
			$sender = $this->requestData['user']['backends']['och']['value']; // copy the params('user') to a variable so it won't be called many times in a large conversation
			$PushMessageMapper = $this->app['PushMessageMapper'];
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
		}*/
		return true;
	}
}
