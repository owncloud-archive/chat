<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use OCA\Chat\OCH\Commands\SyncOnline;

class Online extends ChatAPI {
	
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$mapper = $this->c['UserOnlineMapper'];
		$mapper->updateLastOnline($this->requestData['session_id'], $this->requestData['timestamp']);

		$syncOnline = $this->c['SyncOnlineCommand'];
		$syncOnline->execute();

	}
}
