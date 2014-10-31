<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
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
		$userOnline->setUser($requestData['user']['id']);
		$userOnline->setSessionId($sessionId);
		$userOnline->setLastOnline($requestData['timestamp']);
		$mapper = $this->c['UserOnlineMapper'];
		$mapper->insert($userOnline);


		// The user is now online
		// send a push message to every user to inform this
		$command = json_encode(array(
			'type' => 'online',
			'data' => array(
				'user' => $this->requestData['user'],
				'timestamp' => $this->requestData['timestamp'],
			)
		));

		$pushMessageMapper = $this->c['PushMessageMapper'];
		$pushMessageMapper->createForAllSessions(
			$this->requestData['user']['id'],
			$command
		);

		return array("session_id" => $sessionId);
	}

	private function generateSessionId($timestamp){
		$seed = "sessionID" . $timestamp;
		return md5($seed);
	}

}
