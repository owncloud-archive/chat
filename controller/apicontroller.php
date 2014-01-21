<?php

namespace OCA\Chat\Controller;

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

use OCP\AppFramework\Http\JSONResponse;

use \OCA\Chat\Push\Get;
use \OCA\Chat\Push\Delete;

use \OCP\AppFramework\Controller;
use \OCA\Chat\Core\API;
use \OCP\IRequest;
use \OCP\AppFramework\IAppContainer;



class ApiController extends Controller {	

    public function __construct(IAppContainer $app, IRequest $request){
		parent::__construct($app, $request);
	}
  
    /**
	 * Routes the API Request
     * @param String $this->params('JSON') command in JSON
     * @return JSONResponse 
	 * @NoCSRFRequired
	 * @NoAdminRequired
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
							if($request['data']['user'] === $this->app->getCoreApi()->getUserId()){
								
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
									$commandClass = new $className($this->app->getCoreApi());
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
   					if($request['data']['user'] === $this->app->getCoreApi()->getUserId()){
   						if(!empty($request['data']['session_id'])){
   							//throw new \Exception("good to go");
   							$pushClasses = array(
   								"get" => "\OCA\Chat\Push\Get",
								"delete" => "\OCA\Chat\Push\Delete"
   							);
   							$className = $pushClasses[$action];
							$pushClass = new $className($this->app->getCoreApi());
  							//throw new \Exception("ok");

							$pushClass->setRequestData($request['data']);

							return $pushClass->execute();
  							//throw new \Exception("ok");

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