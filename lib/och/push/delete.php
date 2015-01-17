<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Push;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCP\AppFramework\Http\JSONResponse;

class Delete extends ChatAPI{

	/**
	 * @var $pushMessageMapper \OCA\Chat\OCH\Db\PushMessageMapper
	 */
	private $pushMessageMapper;

	public function __construct(
		PushMessageMapper $pushMessageMapper
	) {
		$this->pushMessageMapper = $pushMessageMapper;
	}

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		foreach($this->requestData['ids'] as $id){
			$pushMessage = new PushMessage();
			$pushMessage->setId($id);
			$this->pushMessageMapper->delete($pushMessage);
		}
		return new JSONResponse(array('status' => 'success'));
	}
}
