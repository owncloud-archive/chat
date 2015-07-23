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
	 * @var $pushMessageMapper \OCA\Chat\OCH\Db\PushMessageMapper
	 */
	private $pushMessageMapper;

	/**
	 * @var $messageMapper \OCA\Chat\OCH\Db\messageMapper
	 */
	private $messageMapper;

	public function __construct(
		PushMessageMapper $pushMessageMapper,
		MessageMapper $messageMapper
	){
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
		$command = json_encode(array(
			'type' => 'send_chat_msg',
			'data' => array(
				'user' => $this->requestData['user'],
				'conv_id' => $this->requestData['conv_id'],
				'timestamp' => $this->requestData['timestamp'],
				'chat_msg' => $this->requestData['chat_msg']
			)
		));

		$this->pushMessageMapper->createForAllUsersInConv(
			$this->requestData['user']['id'],
			$this->requestData['conv_id'],
			$command,
			$this->requestData['user']['id']
		);

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
