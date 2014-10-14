<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use OCA\Chat\OCH\Db\Attachment;
use \OCA\Chat\OCH\Db\PushMessage;

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
		$paths = $this->requestData['paths'];
		$userMapper = $this->c['UserMapper'];
		$users = $userMapper->findUsersInConv($this->requestData['conv_id']);
		foreach ($paths as $path) {
			$fileId = $this->app->getFileId($path);
			$this->insertInDatabase(
				$this->app->getUserId(),
				$path,
				$fileId,
				$this->requestData['timestamp'],
				$this->requestData['conv_id']
			);
			$this->sendPushMessage($path);
		}
		foreach ($users as $user) {
			if ($user !== $this->app->getUserId()) {
				foreach ($paths as $path) {
					$fileId = $this->app->getFileId($path);
					$this->share($fileId, $user);
				}
			}
		}
	}

	private function sendPushMessage($path){
		$userMapper = $this->c['UserMapper'];
		$users = $userMapper->findSessionsByConversation($this->requestData['conv_id']);
		$pushMessageMapper = $this->c['PushMessageMapper'];
		$command = json_encode(array(
			'type' => 'file_attached',
			'data' => array(
				'user' => $this->requestData['user'],
				'conv_id' => $this->requestData['conv_id'],
				'timestamp' => $this->requestData['timestamp'],
				'path' => $path
			)
		));
		foreach($users as $receiver) {
			if($receiver->getUser() !== $this->requestData['user']['backends']['och']['value']) {
				$pushMessage = new PushMessage();
				$pushMessage->setSender($this->requestData['user']['backends']['och']['value']);
				$pushMessage->setReceiver($receiver->getUser());
				$pushMessage->setReceiverSessionId($receiver->getSessionId());
				$pushMessage->setCommand($command);
				$pushMessageMapper->insert($pushMessage);
			}
		}
	}
	
	/**
	 * Inserts the attachment into the DB
	 * @param $ownerId ownCloud user id
	 * @param $path path of the file
	 * @param $fileId
	 * @param $timestamp
	 * @param $convId
	 */
	private function insertInDatabase($ownerId, $path, $fileId, $timestamp, $convId){
		$attachment = new Attachment();
		$attachment->setOwner($ownerId);
		$attachment->setPath($path);
		$attachment->setFileId($fileId);
		$attachment->setTimestamp($timestamp);
		$attachment->setConvId($convId);
		$attachmentMapper = $this->c['AttachmentMapper'];
		$attachmentMapper->insertUnique($attachment);
	}

	/**
	 * @param $fileId the fileId of the file
	 * @param $shareWIth the ownCloud user to share the file with
	 */
	private function share($fileId, $shareWIth){
		try {
			\OCP\Share::shareItem('file', $fileId, \OCP\Share::SHARE_TYPE_USER, $shareWIth, \OCP\PERMISSION_ALL);
		} Catch (\Exception $e){

		}
	}

}
