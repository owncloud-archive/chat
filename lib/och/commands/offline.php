<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Commands\SyncOnline;
use \OCA\Chat\OCH\Db\PushMessage;

class Offline extends ChatAPI {


	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$mapper = $this->c['UserOnlineMapper'];
		$mapper->deleteBySessionId($this->requestData['session_id']);

		$syncOnline = $this->c['SyncOnlineCommand'];
		$syncOnline->execute();

		$this->sendOfflineMsg();
	}

	private function sendOfflineMsg(){
		// first check if we're really offline
		$mapper = $this->c['UserOnlineMapper'];
		$sessions = $mapper->findByUser($this->requestData['user']['backends']['och']['value']);
		if(count($sessions) === 0){
			$command = json_encode(array(
				'type' => 'offline',
				'data' => array(
					'user' => $this->requestData['user'],
					'timestamp' => $this->requestData['timestamp'],
				)
			));

			$userOnlineMapper = $this->c['UserOnlineMapper'];
			$users = $userOnlineMapper->getAll();

			$sender = $this->requestData['user']['backends']['och']['value'];
			$pushMessageMapper = $this->c['PushMessageMapper'];

			foreach($users as $user){
				if($user->getUser() !== $sender){
					$pushMessage = new PushMessage();
					$pushMessage->setSender($sender);
					$pushMessage->setReceiver($user->getUser());
					$pushMessage->setReceiverSessionId($user->getSessionId());
					$pushMessage->setCommand($command);
					$pushMessageMapper->insert($pushMessage);
				}
			}
		}
	}

}

