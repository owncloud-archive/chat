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
use \OCA\CHat\OCH\Db\PushMessageMapper;
use \OCA\Chat\App\Chat;

class Greet extends ChatAPI {

	/**
	 * @var $pushMessageMapper \OCA\Chat\OCH\Db\PushMessageMapper
	 */
	private $pushMessageMapper;

	/**
	 * @var $userOnlineMapper \OCA\Chat\OCH\Db\UserOnlineMapper
	 */
	private $userOnlineMapper;

	public function __construct(
		PushMessageMapper $pushMessageMapper,
		UserOnlineMapper $userOnlineMapper
	){
		$this->pushMessageMapper = $pushMessageMapper;
		$this->userOnlineMapper = $userOnlineMapper;
	}

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
		$this->userOnlineMapper->insert($userOnline);


		// The user is now online
		// send a push message to every user to inform this
		$command = json_encode(array(
			'type' => 'online',
			'data' => array(
				'user' => $this->requestData['user'],
				'timestamp' => $this->requestData['timestamp'],
			)
		));

		$this->pushMessageMapper->createForAllSessions(
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
