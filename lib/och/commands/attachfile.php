<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;

class AttachFile extends ChatAPI {

	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['session_id'] String session_id of the client
	 * @param $requestData['timestamp'] Int timestamp when the command was send
	*/
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){

	}

	/**
	 * Inserts the attachment into the DB
	 * @param $ownerId ownCloud user id
	 * @param $path path of the file
	 * @param $timestamp
	 * @param $convId
	 */
	private function insertInDatabase($ownerId, $path, $timestamp, $convId){

	}

	private function share($path, $user){
		\OCP\Share::shareItem('file', $path, \OCP\Share::SHARE_TYPE_USER, $user, \OCP\PERMISSION_ALL);
	}

}
