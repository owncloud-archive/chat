<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use OCA\Chat\Controller\OCH\ApiController;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCA\Chat\OCH\Db\Message;
use OCA\Chat\OCH\Db\MessageMapper;

class SendChatMsg extends ChatAPI {

	public function setRequestData(array $requestData){
		if(empty($requestData['conv_id'])){
			throw new RequestDataInvalid(ApiController::NO_CONV_ID);
		}
		if(empty($requestData['chat_msg']) || !array_key_exists('chat_msg', $requestData)){
			throw new RequestDataInvalid(ApiController::NO_CHAT_MSG);
		}
		if(empty($requestData['timestamp'])){
			throw new RequestDataInvalid(ApiController::NO_TIMESTAMP);
		}
		$this->requestData = $requestData;
	}

	public function execute(){
		$userMapper = $this->c['UserMapper'];
		$users = $userMapper->findSessionsByConversation($this->requestData['conv_id']);

		$command = json_encode(array(
			'type' => 'send_chat_msg',
			'data' => array(
				'user' => $this->requestData['user'],
				'conv_id' => $this->requestData['conv_id'],
				'timestamp' => $this->requestData['timestamp'],
				'chat_msg' => $this->requestData['chat_msg']
			)
		));

		$sender = $this->requestData['user']['backends']['och']['value'];
		$pushMessageMapper = $this->c['PushMessageMapper'];

		foreach($users as $receiver){
			if($receiver->getUser() !== $sender){
				$pushMessage = new PushMessage();
				$pushMessage->setSender($sender);
				$pushMessage->setReceiver($receiver->getUser());
				$pushMessage->setReceiverSessionId($receiver->getSessionId());
				$pushMessage->setCommand($command);
				$pushMessageMapper->insert($pushMessage);
			}
		}

		// All done
		// insert this chatMsg into the messages table
		$messageMapper = $this->c['MessageMapper'];
		$message = new Message();
		$message->setConvid($this->requestData['conv_id']);
		$message->setTimestamp($this->requestData['timestamp']);
		$message->setUser($this->requestData['user']['backends']['och']['value']);
		$message->setMessage($this->requestData['chat_msg']);
		$messageMapper->insert($message);
	}
}
