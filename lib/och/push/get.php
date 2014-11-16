<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Push;

use OCA\Chat\Controller\OCH\ApiController;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\Db\DoesNotExistException;
use \OCP\AppFramework\Http\JSONResponse;
use \OCA\Chat\OCH\Responses\Error;

class Get extends ChatAPI{

	private $cycles = 0;

	private $pushMessages = array();
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		session_write_close();
		$mapper = $this->c['PushMessageMapper'];

		do {
			if ($this->cycles > 0){
				sleep(1);
			}
			try {
				$this->pushMessages = $mapper->findBysSessionId($this->requestData['session_id']);
				break;
			} catch(DoesNotExistException $e){
			}
			$this->cycles++;
		} while ($this->cycles < 50);

		if(count($this->pushMessages) > 0) {
			$commands = array();
			foreach ($this->pushMessages as $pushMessage) {
				$command = json_decode($pushMessage->getCommand());
				$commands[$pushMessage->getId()] = $command;
			}
			return new JSONResponse(array('push_msgs' => $commands));
		} else {
			return new Error('push', 'get', ApiController::TIME_EXCEEDED);
		}
	}
}
