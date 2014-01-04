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
use \OCA\Chat\Commands\Quit;
use \OCA\Chat\Commands\Leave;
use \OCA\Chat\Commands\Online;
use \OCA\Chat\Commands\checkOnline;

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
   		}catch (UserNotOnlineException $e) {
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
   		try {
   			$leave = new Leave($this->api, $this->getParams());
   			$leave->execute();
   			return new JSONResponse(array('status' => 'success'));
   		} catch(exception $e){
   		
   		}
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
		// TODO catch error when user isn't online
		try {
			$quit = new Quit($this->api, $this->getParams());
			$quit->execute();
			return new JSONResponse(array('status' => 'success'));			
		} catch (exception $e){
		
		}
	}
	
	/**
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 */
	public function online(){
		// TODO catch error when user isn't online
		$online = new Online($this->api, $this->getParams());
		$online->execute();
		
		$checkOnline = new checkOnline($this->api, $this->getParams());
		$checkOnline->execute();
		return new JSONResponse(array('status' => 'success'));
		
	}
}