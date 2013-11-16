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



class Invite extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}
	
	public function execute(){
		$userOnlineMapper = new UserOnlineMapper($this->api);
   		$usersOnline = $userOnlineMapper->getOnlineUsers();

   		if($this->params('user') !== $this->params('usertoinvite')){
	    	if(in_array($this->params('usertoinvite'), \OCP\User::getUsers())){
				if(in_array($this->params('usertoinvite'), $usersOnline)){
					// First fetch every sessionID of the user to invite
					$userOnlineMapper = new UserOnlineMapper($this->api);
					$pushMessageMapper = new PushMessageMapper($this->api);
					
					$command = json_encode(array('type' => 'invite',
																'param' => array(	'user' => $this->params('user'),	
																					'conversationID' => $this->params('conversationID'),
														
																				'usertoinvite' => $this->params('usertoinvite'))));
																					
					$UTISessionID = $userOnlineMapper->findByUser($this->params('usertoinvite')); // $UTISessionID = UserToInviteSessionId = array()
					
					foreach($UTISessionID as $userToInvite){
						$pushMessage = new PushMessage();
						$pushMessage->setSender($this->params('user'));
						$pushMessage->setReceiver($userToInvite->getUser());
						$pushMessage->setReceiverSessionId($userToInvite->getSessionId());
						
						$pushMessage->setCommand($command);
						$pushMessageMapper->insert($pushMessage);	
					}
					return true;					
				} else {
					return UserNotOnlineException('USER-TO-INVITE-NOT-ONLINE');
				}   			
	   		} else { 
	    		return UserToInviteNotOnlineException('USER-TO-INVITE-NOT-OC-USER');
	   		}
   		} else {
   			return new UserEqualToUserToInvite('USER-EQAUL-TO-USER-TO-INVITE');
   		}
	}	

}
