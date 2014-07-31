<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use OCA\Chat\OCH\Commands\SyncOnline;
use \OCA\Chat\OCH\Db\PushMessage;

class Online extends ChatAPI {
	
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$mapper = $this->app['UserOnlineMapper'];
		$mapper->updateLastOnline($this->requestData['session_id'], $this->requestData['timestamp']);

		$syncOnline = $this->app['SyncOnlineCommand'];
		$syncOnline->execute();

		// The user is now online
		// check if it was online before -> if so do noting -> if not send a push msg
		// send a push message to every user to inform this
		$command = json_encode(array(
			'type' => 'online',
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
