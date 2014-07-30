<?php

namespace OCA\Chat\OCH\Push;

use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\Db\DoesNotExistException;
use \OCP\AppFramework\Http\JSONResponse;
use \OCA\Chat\OCH\Responses\Error;

class Get extends ChatAPI{

	private $cycles = 0;

	private $state;
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		session_write_close();
		try {
			if($this->cycles > (ini_get('max_execution_time') - 5)){
				$this->state = 'TIME-EXCEEDED';
			} else {
				$mapper = $this->app['PushMessageMapper']; // inject API class for db access
				$this->pushMessages = $mapper->findBysSessionId($this->requestData['session_id']);

				$this->state = 'PUSH';
			}
		} catch(DoesNotExistException $e){
			sleep(1);
			$this->cycles++;
			$this->execute();
		}

		if($this->state === 'TIME-EXCEEDED'){
			// out of time
			return new Error('push', 'get', 'TIME_EXCEEDED');
		} else {
			// Found push Messages
			$commands = array();
			foreach($this->pushMessages as $pushMessage){
				$command = json_decode($pushMessage->getCommand());
				$commands[$pushMessage->getId()] = $command;
			}
			return new JSONResponse(array('push_msgs' => $commands));
		}
	}
}
