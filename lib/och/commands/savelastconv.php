<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Db\ConfigMapper;

class SaveLastConv extends ChatAPI {

	/**
	 * @var $configMapper \OCA\Chat\Db\ConfigMapper
	 */
	private $configMapper;

	public function __construct(
		ConfigMapper $configMapper
	){
		$this->configMapper = $configMapper;
	}

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$this->configMapper->set('och', 'last_active_conv', $this->requestData['conv_id'], false);
	}
}
