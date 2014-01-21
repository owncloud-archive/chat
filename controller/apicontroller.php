<?php

namespace OCA\Chat\Controller;

use \OCP\AppFramework\Controller;
use \OCA\Chat\Core\API;
use \OCA\Chat\Commands\Greet;
use \OCA\Chat\Commands\Join;
use \OCA\Chat\Commands\Invite;
use \OCA\Chat\Commands\Send;
use \OCA\Chat\Commands\GetConversations;
use \OCA\Chat\Commands\Quit;
use \OCA\Chat\Commands\Leave;
use \OCA\Chat\Commands\Online;
use \OCA\Chat\Commands\checkOnline;
use \OCA\Chat\Responses\Success;
use \OCA\Chat\Responses\Error;
use \OCA\Chat\Exceptions\RequestDataInvalid;

use \OCP\Chat\Http\JSONResponse;

use \OCA\Chat\Push\Get;
use \OCA\Chat\Push\Delete;

class ApiController extends Controller {	

    /**
     * @param Request $request an instance of the request
     * @param API $api an api wrapper instance
     */
    public function __construct($api, $request){
        parent::__construct($api, $request);
    }
  
    /**
	 * Routes the API Request
     * @param String $this->params('JSON') command in JSON
     * @return JSONResponse 
     * @IsAdminExemption
     * @IsSubAdminExemption
     */
    public function route(){
		$request = json_decode($this->params('JSON'), true);
		list($requestType, $action, $http_type) = explode("::", $request['type']);

		if($http_type === "request"){

			// Check request type 
			switch($requestType){
				case "command":
					// $action is the type of the command

					$possibleCommands = array('greet', 'join', 'invite', 'leave', 'send_chat_msg', 'quit', 'online');
					if(in_array($action, $possibleCommands)){
						if(!empty($request['data']['session_id'])){
							if($request['data']['user'] === $this->api->getUserId()){
								
								$commandClasses = array(
									'greet' => '\OCA\Chat\Commands\Greet',
									'join' => '\OCA\Chat\Commands\Join',
									'invite' => '\OCA\Chat\Commands\Invite',
									'leave' => '\OCA\Chat\Commands\Leave',
									'send_chat_msg' => '\OCA\Chat\Commands\SendChatMsg',
									'quit' => '\OCA\Chat\Commands\Quit',
									'online' => '\OCA\Chat\Commands\Online'
								);
								
								try{
									$className = $commandClasses[$action];
									$commandClass = new $className($this->api);
									$commandClass->setRequestData($request['data']);
									$commandClass->execute();

									return new Success("command", $action);
								}catch(RequestDataInvalid $e){
									return new Error("command", $action, $e->getMessage());
								}
							} else {
								return new Error("command", $action, "USER-NOT-EQUAL-TO-OC-USER");
							}
						} else {
							return new Error("command", $action, "SESSION-ID-NOT-PROVIDED"); 
						}
					} else {
						return new Error("command", $action, "COMMAND-NOT-FOUND");
					}

					break;
				case "push":
   					if($request['data']['user'] === $this->api->getUserId()){
   						if(!empty($request['data']['session_id'])){
   							$pushClasses = array(
   								"get" => "\OCA\Chat\Push\Get",
								"delete" => "\OCA\Chat\Push\Delete"
   							);
   							$className = $pushClasses[$action];
							$pushClass = new $className($this->api);
							$pushClass->setRequestData($request['data']);
							return $pushClass->execute();

   						} else{
   							return new Error('push', 'session_id not provided');
   							//@todo add better error reporting
   						}
					} else {
						return new Error('push', 'user not ok');
   					}
					break;
				case "data":
					break;
			}

		} else {
			return new Error($requestType, $action, "HTTP-TYPE-INVALID");
		}
	}
}