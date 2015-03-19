<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\Attachment;
use OCA\Chat\OCH\Db\AttachmentMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCA\Chat\Controller\OCH\ApiController;

class RemoveFile extends ChatAPI {

    /**
     * @var $pushMessageMapper \OCA\Chat\OCH\Db\PushMessageMapper
     */
    private $pushMessageMapper;

    /**
     * @var $attachmentMapper \OCA\Chat\OCH\Db\AttachmentMapper
     */
    private $attachmentMapper;

    /**
     * @var $userMapper \OCA\Chat\OCH\Db\UserMapper
     */
    private $userMapper;

    public function __construct(
        Chat $app,
        PushMessageMapper $pushMessageMapper,
        AttachmentMapper $attachmentMapper,
        UserMapper $userMapper
    ){
        $this->app = $app;
        $this->pushMessageMapper = $pushMessageMapper;
        $this->attachmentMapper = $attachmentMapper;
        $this->userMapper = $userMapper;
    }



	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['session_id'] String session_id of the client
	 * @param $requestData['timestamp'] Int timestamp when the command was send
	*/
	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
        $attachment = $this->attachmentMapper->findByPathAndConvId($this->requestData['path'], $this->requestData['conv_id']);
        if ($attachment->getOwner() !== $this->app->getUserId()){
            throw new RequestDataInvalid(ApiController::NOT_OWNER_OF_FILE);
        }
    }

	public function execute(){

		$fileId = $this->app->getFileId($this->requestData['path']);
		$file = new Attachment();
		$file->setConvId($this->requestData['conv_id']);
		$file->setFileId($fileId);
		$this->attachmentMapper->deleteByConvAndFileID($file);
        $this->sendPushMessage($this->requestData['path']);
		$users = $this->userMapper->findUsersInConv($this->requestData['conv_id']);
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
        $users = $this->userMapper->findSessionsByConversation($this->requestData['conv_id']);
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


}
