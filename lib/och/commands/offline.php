<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Commands\SyncOnline;
use \OCA\Chat\OCH\Db\PushMessage;

class Offline extends ChatAPI {


	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$mapper = $this->app['UserOnlineMapper'];
		$mapper->deleteBySessionId($this->requestData['session_id']);

		$syncOnline = $this->app['SyncOnlineCommand'];
		$syncOnline->execute();

		$this->sendOfflineMsg();
	}

	private function sendOfflineMsg(){
		// first check if we're really offline
		$mapper = $this->app['UserOnlineMapper'];
		$sessions = $mapper->findByUser($this->requestData['user']['backends']['och']['value']);
//		var_dump($sessions);
//		die();
		if(count($sessions) === 0){
			$command = json_encode(array(
				'type' => 'offline',
				'data' => array(
					'user' => $this->requestData['user'],
					'timestamp' => $this->requestData['timestamp'],
				)
			));

			$userOnlineMapper = $this->app['UserOnlineMapper'];
			$users = $userOnlineMapper->getAll();

			$sender = $this->requestData['user']['backends']['och']['value'];
			$pushMessageMapper = $this->app['PushMessageMapper'];

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

