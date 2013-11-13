<?php

namespace OCA\Chat\Controller;

use \OCA\AppFramework\Controller\Controller;
use \OCA\AppFramework\Http\JSONResponse;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Db\PushMessage;
use \OCA\Chat\Db\PushMessageMapper;
use \OCA\Chat\Db\Conversation;
use \OCA\Chat\Db\ConversationMapper;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;

class CommandController extends Controller {	

    /**
     * @param Request $request an instance of the request
     * @param API $api an api wrapper instance
     */
    public function __construct($api, $request){
        parent::__construct($api, $request);
    }
  
    /**
     * @CSRFExemption
     * @IsAdminExemption
     * @IsSubAdminExemption
     */
    public function greet(){
    	if(in_array($this->params('user'), \OCP\User::getUsers())){   		
   		
    		$userOnline = new UserOnline();
    		$userOnline->setUser($this->params('user'));
			$userOnline->setSessionId($this->params('sessionID'));
    		$mapper = new UserOnlineMapper($this->api);
    		$mapper->insert($userOnline);
    		
    		
    		return new JSONResponse(array('status' => 'success'));
    	} else {
    		return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'NO-OC-USER')));
    	}
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function join(){
		
   		if(in_array($this->params('user'), \OCP\User::getUsers())){ 

	   		$userMapper = new UserMapper($this->api);
	   		$users = $userMapper->findByConversation($this->params('conversationID'));
	   		
	   		if (count($users) === 0){
	   			$conversation = new Conversation();
	   			$conversation->setConversationId($this->params('conversationID'));
	   			$mapper = new ConversationMapper($this->api); 
	   			$mapper->insert($conversation);
	   			 
	   			$user = new User();
	   			$user->setConversationId($this->params('conversationID'));
	   			$user->setUser($this->params('user'));
				$user->setSessionId($this->params('sessionID'));
	   			$userMapper = new UserMapper($this->api);
	   			$userMapper->insert($user);
	   			
	   			return new JSONResponse(array('status' => 'success'));
	   		} else { 
	   			$user = new User();
	   			$user->setConversationId($this->params('conversationID'));
	   			$user->setUser($this->params('user'));
				$user->setSessionId($this->params('sessionID'));
	   			$userMapper = new UserMapper($this->api);
	   			$userMapper->insert($user);

				$conversationMapper = new ConversationMapper($this->api);
				$conversationMapper->updateName($this->params('conversationID'));	   			
	   			return new JSONResponse(array('status' => 'success'));
	   		} 
	   	} else {
	   		return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'NO-OC-USER')));
	   	}
   		
   	}
   	
    

   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function invite(){
		
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
						\OCP\Util::writeLog('chat', 'session ID: user to invite' . $userToInvite->getSessionId(), \OCP\Util::ERROR);
						
						$pushMessage->setCommand($command);
						$pushMessageMapper->insert($pushMessage);	
					}
					
						
					
					return new JSONResponse(array('status' => 'success'));
				} else {
					return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'USER-TO-INVITE-NOT-ONLINE')));
				}   			
	   		} else { 
	    		return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'USER-TO-INVITE-NOT-OC-USER')));
	   		}
   		} else {
   			return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'USER-EQAUL-TO-USER-TO-INVITE')));
   		}
   	}
   	
    // Functions below are place holders
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function leave(){
   		return new JSONResponse(array('status' => $this->params('user'),
   				'conversationID' => $this->params('conversationID'),
   		));
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function getusers(){
   		return new JSONResponse(array('conversationID' => $this->params('conversationID'),
   		));
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function send(){
   		$userMapper = new UserMapper($this->api);
	   	$users = $userMapper->findByConversation($this->params('conversationID'));
		$command = json_encode(array('type' => 'send', 'param' => array('user' => $this->params('user'), 'conversationID' => $this->params('conversationID'), 'msg' => $this->params('msg'))));	
		$sender = $this->params('user'); // copy the params('user') to a variable so it won't be called many times in a large conversation
		$PushMessageMapper = new PushMessageMapper($this->api);
		
		foreach($users as $receiver){
			$pushMessage = new PushMessage();
			$pushMessage->setSender($sender);
			$pushMessage->setReceiver($receiver->getUser());
			$pushMessage->setReceiverSessionId($receiver->getSessionId());
			$pushMessage->setCommand($command);
			$PushMessageMapper->insert($pushMessage);	
		}
		
   		return new JSONResponse(array('status' => 'success'));
   	}
	
	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
	public function getConversations(){
		$userMapper = new UserMapper($this->api);
		$conversations = $userMapper->findByUser($this->params('user'));
		$response = array();
		foreach($conversations as $conversation){
			array_push($response, $conversation->getConversationId());
		}
		return new JSONResponse(array('status' => 'success', 'data'=> array('param' => array('conversations' => $response))));
	}

	/**
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
	public function quit(){
		\OCP\Util::writeLog('chat', 'quit ' . $this->params('sessionID'), \OCP\Util::ERROR);
		
		// First delete the sessionid from the online user table
		$userOnlineMapper = new UserOnlineMapper($this->api);
		$userOnlineMapper->deleteBySessionId($this->params('sessionID'));
		
		// Next leave all conversations by sessionID
		$userMapper = new UserMapper($this->api);
		$userMapper->deleteBySessionId($this->params('sessionID'));
		return new JSONResponse(array('status' => 'success'));
	}
}