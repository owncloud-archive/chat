<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\Attachment;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCA\Chat\Controller\OCH\ApiController;

class RemoveFile extends ChatAPI {

	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['session_id'] String session_id of the client
	 * @param $requestData['timestamp'] Int timestamp when the command was send
	*/
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
        $attachmentMapper = $this->c['AttachmentMapper'];
        $attachment = $attachmentMapper->findByPathAndConvId($this->requestData['path'], $this->requestData['conv_id']);
        if ($attachment->getOwner() !== $this->app->getUserId()){
            throw new RequestDataInvalid(ApiController::NOT_OWNER_OF_FILE);
        }
    }

	public function execute(){

		$fileId = $this->app->getFileId($this->requestData['path']);
		$attachmentMapper = $this->c['AttachmentMapper'];
		$file = new Attachment();
		$file->setConvId($this->requestData['conv_id']);
		$file->setFileId($fileId);
		$attachmentMapper->deleteByConvAndFileID($file);
        $this->sendPushMessage($this->requestData['path']);
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

    private function sendPushMessage($path){
        $userMapper = $this->c['UserMapper'];
        $users = $userMapper->findSessionsByConversation($this->requestData['conv_id']);
        $pushMessageMapper = $this->c['PushMessageMapper'];
        $command = json_encode(array(
            'type' => 'file_removed',
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


}
