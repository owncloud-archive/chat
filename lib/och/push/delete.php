<?php

namespace OCA\Chat\OCH\Push;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\Core\API;
use \OCP\AppFramework\Http\JSONResponse;

class Delete extends ChatAPI{

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		foreach($this->requestData['ids'] as $id){
			$pushMessage = new PushMessage();
			$pushMessage->setId($id);
			$mapper = $this->app['PushMessageMapper'];
			$mapper->delete($pushMessage);
		}
		return new JSONResponse(array('status' => 'success'));
	}
}
