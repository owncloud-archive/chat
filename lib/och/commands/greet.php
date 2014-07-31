<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnline;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Db\PushMessage;

class Greet extends ChatAPI {

	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['session_id'] String session_id of the client
	 * @param $requestData['timestamp'] Int timestamp when the command was send
	*/
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$requestData = $this->getRequestData();
		$sessionId = $this->generateSessionId($requestData['timestamp']);
		$userOnline = new UserOnline();
		$userOnline->setUser($requestData['user']['backends']['och']['value']);
		$userOnline->setSessionId($sessionId);
		$userOnline->setLastOnline($requestData['timestamp']);
		$mapper = $this->app['UserOnlineMapper'];
		$mapper->insert($userOnline);


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


		return array("session_id" => $sessionId);
	}

	private function generateSessionId($timestamp){
		$seed = "sessionID" . $timestamp;
		return md5($seed);
	}

}
