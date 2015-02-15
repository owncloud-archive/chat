<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\CHat\App\Chat;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Commands\SyncOnline;

class Online extends ChatAPI {

	/**
	 * @var $userMapper \OCA\Chat\OCH\Db\UserOnlineMapper
	 */
	private $userOnlineMapper;

	/**
	 * @var $syncOnline \OCA\Chat\OCH\Commands\SyncOnline
	 */
	private $syncOnline;

	public function __construct(
		UserOnlineMapper $userOnlineMapper,
		SynConline $syncOnline
	){
		$this->userOnlineMapper = $userOnlineMapper;
		$this->syncOnline = $syncOnline;

	}

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$this->userOnlineMapper->updateLastOnline($this->requestData['session_id'], $this->requestData['timestamp']);
		$this->syncOnline->execute();
	}
}
