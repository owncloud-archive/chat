<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\Attachment;
use \OCA\Chat\OCH\Db\AttachmentMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCP\Share;

class AttachFile extends ChatAPI {

	/**
	 * @var $userMapper \OCA\Chat\OCH\Db\UserMapper
	 */
	private $userMapper;

	/**
	 * @var $attachmentMapper \OCA\Chat\OCH\Db\AttachmentMapper
	 */
	private $attachmentMapper;

	/**
	 * @var $pushMessageMapper \OCA\Chat\OCH\Db\PushMessageMapper
	 */
	private $pushMessageMapper;

	public function __construct(
		Chat $app,
		UserMapper $userMapper,
		AttachmentMapper $attachmentMapper,
		PushMessageMapper $pushMessageMapper
	){
		$this->app = $app;
		$this->userMapper = $userMapper;
		$this->attachmentMapper = $attachmentMapper;
		$this->pushMessageMapper = $pushMessageMapper;
	}
	
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
		$users = $this->userMapper->findUsersInConv($this->requestData['conv_id']);
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
		$users = $this->userMapper->findSessionsByConversation($this->requestData['conv_id']);
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
			if($receiver->getUser() !== $this->requestData['user']['id']) {
				$pushMessage = new PushMessage();
				$pushMessage->setSender($this->requestData['user']['id']);
				$pushMessage->setReceiver($receiver->getUser());
				$pushMessage->setReceiverSessionId($receiver->getSessionId());
				$pushMessage->setCommand($command);
				$this->pushMessageMapper->insert($pushMessage);
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
		$this->attachmentMapper->insertUnique($attachment);
	}

	/**
	 * @param $fileId the fileId of the file
	 * @param $shareWIth the ownCloud user to share the file with
	 */
	private function share($fileId, $shareWIth){
		try {
			Share::shareItem('file', $fileId, \OCP\Share::SHARE_TYPE_USER, $shareWIth, \OCP\PERMISSION_ALL);
		} Catch (\Exception $e){

		}
	}

}
