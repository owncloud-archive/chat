<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH;

use \OCA\Chat\App\Chat;

/*
 * API Base Class
 */
abstract class ChatAPI {

	protected $requestData;

	abstract function setRequestData(array $requestData);

	public function getRequestData(){
		return $this->requestData;
	}

	abstract public function execute();
}
