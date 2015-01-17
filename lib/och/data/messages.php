<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Data;

use \OCA\Chat\OCH\ChatAPI;
use OCA\Chat\OCH\Db\MessageMapper;

class Messages extends ChatAPI {

	/**
	 * @var $messageMapper \OCA\Chat\OCH\Db\MessageMapper
	 */
	private $messageMapper;

	public function __construct(
		MessageMapper $messageMapper
	) {
		$this->messageMapper = $messageMapper;
	}

	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['convid'] String session_id of the client
	*/
	public function setRequestData(array $requestData){
	// TODO check if $requestData['user'] is in the $requestData['conv'od] conv
		$this->requestData = $requestData;
	}

	public function execute(){
		$return = array();
		if(isset($this->requestData['startpoint']) && trim($this->requestData['startpoint']) !== ''){
			$startpoint = $this->requestData['startpoint'];
		} else {
			$startpoint = 0;
		}

		$msgs = $this->messageMapper->getMessagesByConvId($this->requestData['conv_id'], $this->requestData['user']['id'], $startpoint);

		foreach($msgs as $msg){
			$return[] = array(
			"id" => $msg->getId(),
			"convid" => $msg->getConvid(),
			"timestamp" => $msg->getTimestamp(),
			"user" => $msg->getUser(),
			"msg" => $msg->getMessage()
			);
		}
		return array("messages" => $return);
	}
}
