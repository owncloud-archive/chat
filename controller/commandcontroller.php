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
use \OCA\Chat\Exceptions\NoOcUserException;
use \OCA\Chat\Exceptions\UserNotOnlineException;
use \OCA\Chat\Exceptions\UserToInviteNotOnlineException;
use \OCA\Chat\Exceptiosn\UserEqualToUserToInvite;
use \OCA\Chat\Commands\Greet;
use \OCA\Chat\Commands\Join;
use \OCA\Chat\Commands\Invite;
use \OCA\Chat\Commands\Send;
use \OCA\Chat\Commands\GetConversations;

class CommandController extends Controller {	

    /**
     * @param Request $request an instance of the request
     * @param API $api an api wrapper instance
     */
    public function __construct($api, $request){
        parent::__construct($api, $request);
    }
  
    /**
     * @IsAdminExemption
     * @IsSubAdminExemption
     */
    public function greet(){
		try {
			$greet = new Greet($this->api, $this->getParams());
			$greet->execute();
			return new JSONResponse(array('status' => 'success'));
		} catch (NoOcUserException $e) {
			return new JSONResponse(array('status' => 'error', 'data' => array('msg' => $e->getMessage()))); 
		}
   	}
   	
   	/**
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function join(){
		try {
			$join = new Join($this->api, $this->getParams());
			$join->execute();
			return new JSONResponse(array('status' => 'success'));
		} catch (NoOcUserException $e) {
			return new JSONResponse(array('status' => 'error', 'data' => array('msg' => $e->getMessage()))); 
		}
   	}
   	
   	/**
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function invite(){
		try {
			$invite = new Invite($this->api, $this->getParams());
			$invite->execute();
			return new JSONResponse(array('status' => 'success'));
		} catch (UserNotOnlineException $e) {
			
			return new JSONResponse(array('status' => 'error', 'data' => array('msg' => $e->getMessage()))); 
		
		} catch (UserToInviteNotOnlineException $e) {
			
			return new JSONResponse(array('status' => 'error', 'data' => array('msg' => $e->getMessage()))); 
		
		} catch (UserEqualToUserToInvite $e) {
			
			return new JSONResponse(array('status' => 'error', 'data' => array('msg' => $e->getMessage()))); 
		
		}
   	}
   	
   	/**
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function leave(){
   		return new JSONResponse(array('status' => $this->params('user'),
   				'conversationID' => $this->params('conversationID'),
   		));
   	}
   	
   	/**
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function getusers(){
   		return new JSONResponse(array('conversationID' => $this->params('conversationID'),
   		));
   	}
   	
   	/**
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function send(){
   		try {
   			$send = new Send($this->api, $this->getParams());
			$send->execute();
			return new JSONResponse(array('status' => 'success'));
   		} catch(exception $e){
   			
		}
   	}
	
	/**
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
	public function getConversations(){
		try {
			$getConversations = new GetConversations($this->api, $this->getParams());
			$response = $getConversations->execute();
			return new JSONResponse(array('status' => 'success', 'data'=> array('param' => array('conversations' => $response))));
		} catch (Exception $e){
			
		}
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