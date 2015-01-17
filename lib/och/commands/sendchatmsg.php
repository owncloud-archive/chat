<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\App\Chat;
use \OCA\Chat\Controller\OCH\ApiController;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCA\Chat\OCH\Db\Message;
use \OCA\Chat\OCH\Db\MessageMapper;

class SendChatMsg extends ChatAPI {

	/**
	 * @var $userMapper \OCA\Chat\OCH\Db\UserMapper
	 */
	private $userMapper;

	/**
	 * @var $pushMessageMapper \OCA\Chat\OCH\Db\PushMessageMapper
	 */
	private $pushMessageMapper;

	/**
	 * @var $messageMapper \OCA\Chat\OCH\Db\messageMapper
	 */
	private $messageMapper;

	public function __construct(
		Chat $app,
		UserMapper $userMapper,
		PushMessageMapper $pushMessageMapper,
		MessageMapper $messageMapper
	){
		$this->app = $app;
		$this->userMapper = $userMapper;
		$this->pushMessageMapper = $pushMessageMapper;
		$this->messageMapper = $messageMapper;
	}



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
		$users = $this->userMapper->findSessionsByConversation($this->requestData['conv_id']);

		$command = json_encode(array(
			'type' => 'send_chat_msg',
			'data' => array(
				'user' => $this->requestData['user'],
				'conv_id' => $this->requestData['conv_id'],
				'timestamp' => $this->requestData['timestamp'],
				'chat_msg' => $this->requestData['chat_msg']
			)
		));

		$sender = $this->requestData['user']['id'];

		if(!isset($this->requestData['send_to_sender'])){
			$sendToSender = false;
		} else {
			$sendToSender = $this->requestData['send_to_sender'];
		}

		foreach($users as $receiver){
			if($sendToSender === false && $receiver->getUser() !== $sender){
				$pushMessage = new PushMessage();
				$pushMessage->setSender($sender);
				$pushMessage->setReceiver($receiver->getUser());
				$pushMessage->setReceiverSessionId($receiver->getSessionId());
				$pushMessage->setCommand($command);
				$this->pushMessageMapper->insert($pushMessage);
			} else if ($sendToSender === true){
				$pushMessage = new PushMessage();
				$pushMessage->setSender($sender);
				$pushMessage->setReceiver($receiver->getUser());
				$pushMessage->setReceiverSessionId($receiver->getSessionId());
				$pushMessage->setCommand($command);
				$this->pushMessageMapper->insert($pushMessage);
			}
		}

		// All done
		// insert this chatMsg into the messages table
		$message = new Message();
		$message->setConvid($this->requestData['conv_id']);
		$message->setTimestamp($this->requestData['timestamp']);
		$message->setUser($this->requestData['user']['id']);
		$message->setMessage($this->requestData['chat_msg']);
		$this->messageMapper->insert($message);
	}
}
