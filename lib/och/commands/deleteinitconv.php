<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use OCA\Chat\OCH\Db\InitConv;
use OCA\Chat\OCH\Db\InitConvMapper;

class DeleteInitConv extends ChatAPI{

	public function setRequestData(array $requestData) {
		$this->requestData = $requestData;
	}

	public function execute() {
		$initConv = new InitConv();
		$initConv->setConvId($this->requestData['conv_id']);
		$initConv->setUser($this->requestData['user']['backends']['och']['value']);
		$initConvMapper = $this->app['InitConvMapper'];
		$initConvMapper->deleteByConvAndUser($initConv);
	}

}
