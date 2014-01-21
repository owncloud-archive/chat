<?php

namespace OCA\Chat\Commands;

use \OCA\Chat\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Exceptions\UserNotOnlineException;
use \OCA\Chat\Exceptions\UserToInviteNotOnlineException;
use \OCA\Chat\Exceptiosn\UserEqualToUserToInvite;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;
use \OCA\Chat\Db\PushMessage;
use \OCA\Chat\Db\PushMessageMapper;

use \OCA\Chat\Exceptions\RequestDataInvalid;


class Invite extends ChatAPI {
	
	public function __construct(API $api){
		parent::__construct($api);
	}

	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['session_id'] String session_id of the client
	 * @param $requestData['timestamp'] Int timestamp when the command was send
	 * @param $requestData['conv_id'] String id of the conversation
	 * @param $requestData['user_to_invite'] String id of the user which need to be invited
	*/
	public function setRequestData(array $requestData){
		if(empty($requestData['conv_id'])){
			throw new RequestDataInvalid("CONV-ID-MUST-BE-PROVIDED");
		}

		if(empty($requestData['user_to_invite'])){
			throw new RequestDataInvalid("USER-TO-INVITE-MUST-BE-PROVIDED");	
		}

		if($requestData['user'] === $requestData['user_to_invite']){
			throw new RequestDataInvalid("USER-EQAUL-TO-USER-TO-INVITE");
		}

    	if(!in_array($requestData['user_to_invite'], \OCP\User::getUsers())){
    		throw new RequestDataInvalid("USER-TO-INVITE-NOT-OC-USER");
    	}

		$userOnlineMapper = new UserOnlineMapper($this->api);
   		$usersOnline = $userOnlineMapper->getOnlineUsers();
		if(!in_array($requestData['user_to_invite'], $usersOnline)){
			throw new RequestDataInvalid('USER-TO-INVITE-NOT-ONLINE');
		}

		$this->requestData = $requestData;

	}
	
	public function execute(){
		// First fetch every sessionID of the user to invite
		$userOnlineMapper = new UserOnlineMapper($this->api);
		$pushMessageMapper = new PushMessageMapper($this->api);
		
		$command = json_encode(array(
			"type" => "invite",
			"data" => array(
				"user" => $this->requestData['user'],
				"conv_id" => $this->requestData['conv_id'],
				"user_to_invite" => $this->requestData['user_to_invite']
			)
		));
																		
		$UTISessionID = $userOnlineMapper->findByUser($this->requestData['user_to_invite']); // $UTISessionID = UserToInviteSessionId = array()
		
		foreach($UTISessionID as $userToInvite){
			$pushMessage = new PushMessage();
			$pushMessage->setSender($this->requestData['user']);
			$pushMessage->setReceiver($userToInvite->getUser());
			$pushMessage->setReceiverSessionId($userToInvite->getSessionId());
			
			$pushMessage->setCommand($command);
			$pushMessageMapper->insert($pushMessage);	
		}
		return;					
	}	

}
