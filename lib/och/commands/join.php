<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\User;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use OCA\Chat\OCH\Db\InitConv;
use \OCA\Chat\OCH\Data\GetUsers;
use \OCA\Chat\OCH\Data\Messages;


class Join extends ChatAPI {

	public function setRequestData(array $requestData){
		if(empty($requestData['conv_id'])){
			throw new RequestDataInvalid("CONV-ID-MUST-BE-PROVIDED");
		}
		$this->requestData = $requestData;
	}

	public function execute(){
		// mark this conv as a init conv => the conv is auto joined on refresh
		$initConv = new InitConv();
		$initConv->setConvId($this->requestData['conv_id']);
		$initConv->setUser($this->requestData['user']['backends']['och']['value']);
		$initConvMapper = $this->app['InitConvMapper'];
		$initConvMapper->insertUnique($initConv);
		
		// Fetch users in conv
		$getUsers = new GetUsers($this->app);
		$getUsers->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$users = $getUsers->execute();
		$users = $users['users'];
		
		// Fetch messages in conv
		$getMessages = new Messages($this->app);
		$getMessages->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$messages = $getMessages->execute();
		$messages = $messages['messages'];

        if(count($users) > 2){
            // we are in a group conv this mean we have to let the other users now we joined it
            $pushMessageMapper = $this->app['PushMessageMapper'];
            $userMapper = $this->app['UserMapper'];
            $command = json_encode(array(
                "type" => "joined",
                "data" => array(
                    "conv_id" => $this->requestData['conv_id'],
                    "messages" => $messages,
                    "users" => $users
                )
            ));

            $sessions = $userMapper->findSessionsByConversation($this->requestData['conv_id']);
            foreach($sessions as $session){
                $pushMessage = new PushMessage();
                $pushMessage->setSender($this->requestData['user']['backends']['och']['value']);
                $pushMessage->setReceiver($session->getUser());
                $pushMessage->setReceiverSessionId($session->getSessionId());
                $pushMessage->setCommand($command);
                $pushMessageMapper->insert($pushMessage);
            }

        }
		
		return array("messages" => $messages,
					 "users" => $users );
	}
}
