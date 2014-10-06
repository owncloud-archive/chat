<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use OCA\Chat\OCH\Db\Attachment;

class RemoveFile extends ChatAPI {

	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['session_id'] String session_id of the client
	 * @param $requestData['timestamp'] Int timestamp when the command was send
	*/
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){

		$fileId = $this->app->getFileId($this->requestData['path']);
		$attachmentMapper = $this->c['AttachmentMapper'];
		$file = new Attachment();
		$file->setConvId($this->requestData['conv_id']);
		$file->setFileId($fileId);
		$attachmentMapper->deleteByConvAndFileID($file);

		$msg = 'Removed '. $this->requestData['path'] . ' from this conversation';
		$sendChatMsg = $this->c['SendChatMsgCommand'];
		$sendChatMsg->setRequestData(array(
			"conv_id" => $this->requestData['conv_id'],
			"chat_msg" => $msg,
			"timestamp" => $this->requestData['timestamp'],
			"user" => $this->requestData['user'],
			"send_to_sender" => true
		));
		$sendChatMsg->execute();

		$userMapper = $this->c['UserMapper'];
		$users = $userMapper->findUsersInConv($this->requestData['conv_id']);
		foreach ($users as $user) {
			if ($user !== $this->app->getUserId()) {
				$this->unShare($fileId, $user);
			}
		}
	}

	/**
	 * @param $fileId the fileId of the file
	 * @param $shareWIth the ownCloud user to share the file with
	 */
	private function unShare($fileId, $shareWIth){
		try {
			\OCP\Share::unshare('file', $fileId, \OCP\Share::SHARE_TYPE_USER, $shareWIth);
		} Catch (\Exception $e){

		}
	}

}
