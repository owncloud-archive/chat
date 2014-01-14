<?php

namespace OCA\Chat\Controller;

use \OCA\AppFramework\Controller\Controller;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Commands\Greet;
use \OCA\Chat\Commands\Join;
use \OCA\Chat\Commands\Invite;
use \OCA\Chat\Commands\Send;
use \OCA\Chat\Commands\GetConversations;
use \OCA\Chat\Commands\Quit;
use \OCA\Chat\Commands\Leave;
use \OCA\Chat\Commands\Online;
use \OCA\Chat\Commands\checkOnline;
use \OCA\Chat\Commands\SuccessCommandResponse;
use \OCA\Chat\Commands\ErrorCommandResponse;

class ApiController extends Controller {	

    /**
     * @param Request $request an instance of the request
     * @param API $api an api wrapper instance
     */
    public function __construct($api, $request){
        parent::__construct($api, $request);
    }
  
    /**
     * First function called when handling a command
     * Determines which classes are needed.
     * @param String $this->params('type') type of the command
     * @param String $this->params('command') the JSON command 
     * @return JSONResponse 
     * @IsAdminExemption
     * @IsSubAdminExemption
     */
    public function command(){
		$command = json_decode($this->params('command'), true);
		$type = $this->params('type');
		$className = '\OCA\Chat\Commands\\' . ucfirst($type);
		$possibleCommands = array('greet', 'join', 'invite', 'leave', 'send_chat_msg', 'quit', 'online');

		if($command['http_type'] === "request"){
			if(!empty($command['data']['session_id'])){
				if(in_array($this->params('type'), $possibleCommands)){
					if($command['data']['user'] === $this->api->getUserId()){
						$commandClass = new $className($this->api, array());
						$commandClass->setCommandData($command['data']);
						$commandClass->execute();
						return new SuccessCommandResponse($this->params('type'));
					} else {
						return new ErrorCommandResponse($this->params('type'), "USER-NOT-EQUAL-TO-OC-USER");
					}
				} else {
					return new JSONResponse(array(
						"type" => $this->params('type'),
						"http_type"=> "response",
						"data" => array(
						"status" => "error",
							"msg" => "COMMAND-NOT-FOUND"
						)
					));
				}
			} else {
				return new JSONResponse(array(
					"type" => $this->params('type'),
					"http_type"=> "response",
					"data" => array(
					"status" => "error",
						"msg" => "SESSION-ID-NOT-PROVIDED"
					)
				));
			}
		}
   	} 
}