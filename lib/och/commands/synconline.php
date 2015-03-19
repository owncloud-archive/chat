<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\UserOnlineMapper;

// This is the redundant online/offline system
// because sometimes the onbeforeunload does not work we are going to check
// if the current timestamp minus the last online of every sessionid
// (aka the time between now and the last time that the user was online)
// is greater than 60 -> if so make the user offline

class SyncOnline extends ChatAPI {
	/**
	 * @var $userOnlineMapper \OCA\Chat\OCH\Db\userOnlineMapper
	 */
	private $userOnlineMapper;

	public function __construct(
		UserOnlineMapper $userOnlineMapper
	){
		$this->userOnlineMapper = $userOnlineMapper;
	}

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$users = $this->userOnlineMapper->getAll();
		foreach($users as $user){
			if((time() - $user->getLastOnline()) > 70){
				$this->userOnlineMapper->deleteBySessionId($user->getSessionId());
			}
		}
	}
}
