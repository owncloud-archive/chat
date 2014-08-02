<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Data;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;

class GetUsers extends ChatAPI {

	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['convid'] String session_id of the client
	*/
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){
		$userMapper = $this->app['UserMapper'];
		$appApi = $this->app['AppApi'];
		$contacts = $appApi->getContacts();
		$contacts = $contacts['contactsObj'];
		
		$users = $userMapper->findUsersInConv($this->requestData['conv_id']);
		
		$return = array();
		foreach($users as $user){
			$return[] = $contacts[$user];
		}

		// Note: users are full contacts
		return array("users" => $return);
	}
}
