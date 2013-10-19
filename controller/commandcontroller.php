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
     * @IsSubAdminExemptio
     */
    public function greet(){
    	$api = new API();
    	
    	$userOnline = new UserOnline();
    	$userOnline->setUser($this->params('user'));
    	$mapper = new UserOnlineMapper($api);
    	$mapper->insert($userOnline);
    	
    	
    	
    	return new JSONResponse(array('status' => $this->params('user')));
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemptio
   	 */
   	public function join(){
   		// Testing
   		$api = new API();
   		 
   		$conversation = new Conversation();
   		$conversation->setConversationId($this->params('conversationID'));
   		$mapper = new ConversationMapper($api); // inject API class for db access
   		$mapper->insert($conversation);
   		
   		$user = new User();
   		$user->setConversationId($this->params('conversationID'));
   		$user->setUser($this->params('user'));
   		$userMapper = new UserMapper($api);
   		$userMapper->insert($user);
   		
   		
    	return new JSONResponse(array('status' => 'done'));
   	}
    

   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemptio
   	 */
   	public function invite(){
   		return new JSONResponse(array('status' => $this->params('user'),
   										'conversationID' => $this->params('conversationID'),
   										'timestamp' => $this->params('timestamp'),
   										'usertoinvite' => $this->params('usertoinvite')
   									));
   	}
   	
    
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemptio
   	 */
   	public function leave(){
   		return new JSONResponse(array('status' => $this->params('user'),
   				'conversationID' => $this->params('conversationID'),
   		));
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemptio
   	 */
   	public function getusers(){
   		return new JSONResponse(array('conversationID' => $this->params('conversationID'),
   		));
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemptio
   	 */
   	public function send(){
   		$api = new API();
   		
   		// For each user an entry in the pushmessage is necessary
   		// First fetch all users in this conversation from the users_in_conversations table
   		/*
   		 * code block only for information
   		$pushMessage = new PushMessage();  		
   		$pushMessage->setReceiver('testreceiver');
   		$pushMessage->setSender('testsender');
   		$pushMessage->setCommand('testcommadn');
   		$mapper = new pushMessageMapper($api); // inject API class for db access
   		$mapper->insert($pushMessage);
   		
   		*/
   		
   		return new JSONResponse(array('conversationID' => $this->params('conversationID'),
   										'msg' => $this->params('msg'),
   		));
   	}
}