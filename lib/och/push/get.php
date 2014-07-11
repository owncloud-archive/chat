<?php

namespace OCA\Chat\OCH\Push;

use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\Db\DoesNotExistException;
use \OCP\AppFramework\Http\JSONResponse;

class Get extends ChatAPI{

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		session_write_close();
		try {
		$mapper = $this->app['PushMessageMapper']; // inject API class for db access
			$this->pushMessages = $mapper->findBysSessionId($this->requestData['session_id']);
		} catch(DoesNotExistException $e){
			sleep(1);
			$this->execute();
		}

		$commands = array();
		foreach($this->pushMessages as $pushMessage){
//			var_dump($pushMessage->getCommand());
			$command = json_decode($pushMessage->getCommand());
//			var_dump($command);
			$commands[$pushMessage->getId()] = $command;
		}

//		die();
		return new JSONResponse(array('push_msgs' => $commands));
	}
}
