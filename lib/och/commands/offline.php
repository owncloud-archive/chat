<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Commands\SyncOnline;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\App\Chat;

class Offline extends ChatAPI {

	/**
	 * @var $pushMessageMapper \OCA\Chat\OCH\Db\PushMessageMapper
	 */
	private $pushMessageMapper;

	/**
	 * @var $userMapper \OCA\Chat\OCH\Db\UserOnlineMapper
	 */
	private $userOnlineMapper;

	/**
	 * @var $syncOnline \OCA\Chat\OCH\Commands\SyncOnline
	 */
	private $syncOnline;

	public function __construct(
		Chat $app,
		PushMessageMapper $pushMessageMapper,
		UserOnlineMapper $userOnlineMapper,
		SynConline $syncOnline
	){
		$this->app = $app;
		$this->pushMessageMapper = $pushMessageMapper;
		$this->userOnlineMapper = $userOnlineMapper;
		$this->syncOnline = $syncOnline;

	}


	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$this->userOnlineMapper->deleteBySessionId($this->requestData['session_id']);
		$this->syncOnline->execute();
		$this->sendOfflineMsg();
	}

	private function sendOfflineMsg(){
		// first check if we're really offline
		$sessions = $this->userOnlineMapper->findByUser($this->requestData['user']['id']);
		if(count($sessions) === 0){
			$command = json_encode(array(
				'type' => 'offline',
				'data' => array(
					'user' => $this->requestData['user'],
					'timestamp' => $this->requestData['timestamp'],
				)
			));

			$users = $this->userOnlineMapper->getAll();
			$sender = $this->requestData['user']['id'];
			foreach($users as $user){
				if($user->getUser() !== $sender){
					$pushMessage = new PushMessage();
					$pushMessage->setSender($sender);
					$pushMessage->setReceiver($user->getUser());
					$pushMessage->setReceiverSessionId($user->getSessionId());
					$pushMessage->setCommand($command);
					$this->pushMessageMapper->insert($pushMessage);
				}
			}
		}
	}

}

