<?php

namespace OCA\Chat\Commands;

use OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Exceptions\UserNotOnlineException;
use \OCA\Chat\Exceptions\UserToInviteNotOnlineException;
use \OCA\Chat\Exceptiosn\UserEqualToUserToInvite;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;
use \OCA\Chat\Db\PushMessage;
use \OCA\Chat\Db\PushMessageMapper;

use \OCA\Chat\Exceptions\CommandDataInvalid;


class Invite extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}

	/*
	 * @param $commandData['user'] String user id of the client
	 * @param $commandData['session_id'] String session_id of the client
	 * @param $commandData['timestamp'] Int timestamp when the command was send
	 * @param $commandData['conv_id'] String id of the conversation
	 * @param $commandData['user_to_invite'] String id of the user which need to be invited
	*/
	public function setCommandData(array $commandData){
		if(empty($commandData['conv_id'])){
			throw new CommandDataInvalid("CONV-ID-MUST-BE-PROVIDED");
		}

		if(empty($commandData['user_to_invite'])){
			throw new CommandDataInvalid("USER-TO-INVITE-MUST-BE-PROVIDED");	
		}

		if($commandData['user'] === $commandData['user_to_invite']){
			throw new CommandDataInvalid("USER-EQAUL-TO-USER-TO-INVITE");
		}

    	if(!in_array($commandData['user_to_invite'], \OCP\User::getUsers())){
    		throw new CommandDataInvalid("USER-TO-INVITE-NOT-OC-USER");
    	}

		$userOnlineMapper = new UserOnlineMapper($this->api);
   		$usersOnline = $userOnlineMapper->getOnlineUsers();
		if(!in_array($commandData['user_to_invite'], $usersOnline)){
			throw new CommandDataInvalid('USER-TO-INVITE-NOT-ONLINE');
		}

		$this->commandData = $commandData;

	}
	
	public function execute(){
		// First fetch every sessionID of the user to invite
		$userOnlineMapper = new UserOnlineMapper($this->api);
		$pushMessageMapper = new PushMessageMapper($this->api);
		
		$command = json_encode(array(
			"type" => "invite",
			"data" => array(
				"user" => $this->commandData['user'],
				"conv_id" => $this->commandData['conv_id'],
				"user_to_invite" => $this->commandData['user_to_invite']
			)
		));
																		
		$UTISessionID = $userOnlineMapper->findByUser($this->commandData['user_to_invite']); // $UTISessionID = UserToInviteSessionId = array()
		
		foreach($UTISessionID as $userToInvite){
			$pushMessage = new PushMessage();
			$pushMessage->setSender($this->commandData['user']);
			$pushMessage->setReceiver($userToInvite->getUser());
			$pushMessage->setReceiverSessionId($userToInvite->getSessionId());
			
			$pushMessage->setCommand($command);
			$pushMessageMapper->insert($pushMessage);	
		}
		return;					
	}	

}
