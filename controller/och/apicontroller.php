<?php

namespace OCA\Chat\Controller\OCH;
use \OCA\Chat\OCH\Responses\Success;
use \OCA\Chat\OCH\Responses\Error;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Controller;
use \OCP\IRequest;
use \OCP\AppFramework\IAppContainer;
use OCA\Chat\Core\API;
use OCA\Chat\Db\DBException;

class ApiController extends Controller {

	public function __construct(API $api, IRequest $request, IAppContainer $app){
		parent::__construct($api->getAppName(), $request);
		$this->app = $app;
	}

	/**
	 * Routes the API Request
	 * @param String $this->params('JSON') command in JSON
	 * @return JSONResponse
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

				$possibleCommands = array('greet', 'join', 'invite', 'send_chat_msg', 'online', 'offline', 'start_conv');
					if(in_array($action, $possibleCommands)){
						if(!empty($request['data']['session_id'])){
							if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){

								$commandClasses = array(
									'greet' => '\OCA\Chat\OCH\Commands\Greet',
									'join' => '\OCA\Chat\OCH\Commands\Join',
									'invite' => '\OCA\Chat\OCH\Commands\Invite',
									'send_chat_msg' => '\OCA\Chat\OCH\Commands\SendChatMsg',
									'online' => '\OCA\Chat\OCH\Commands\Online',
									'offline' => '\OCA\Chat\OCH\Commands\Offline',
									'start_conv' => '\OCA\Chat\OCH\Commands\StartConv'
								);

								try{
									$className = $commandClasses[$action];
									$commandClass = new $className($this->app);
									$commandClass->setRequestData($request['data']);
									$data = $commandClass->execute();
									if($data){
										return new Success("command", $action, $data);
									} else {
										return new Success("command", $action);
									}
								}catch(DBException $e){
									return new Error("command", $action, "ERROR::DB::" . $e->getMessage());
								}
								catch(RequestDataInvalid $e){
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
					if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
						if(!empty($request['data']['session_id'])){
							$pushClasses = array(
								"get" => "\OCA\Chat\OCH\Push\Get",
								"delete" => "\OCA\Chat\OCH\Push\Delete"
							);
							$className = $pushClasses[$action];
							$pushClass = new $className($this->app);

							$pushClass->setRequestData($request['data']);

							return $pushClass->execute();

						} else{
							return new Error('push', 'SESSION-ID-NOT-PROVIDED');
						}
					} else {
						return new Error('push', 'USER-NOT-EQUAL-TO-OC-USER');
					}
					break;
				case "data":
					if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
						if(!empty($request['data']['session_id'])){
							$pushClasses = array(
								"messages" => "\OCA\Chat\OCH\Data\Messages",
								"contacts" => "\OCA\Chat\OCH\Data\Contacts",
							);
							$className = $pushClasses[$action];
							$dataClass = new $className($this->app);

							$dataClass->setRequestData($request['data']);

							$data = $dataClass->execute();
							if($data){
								return new Success("command", $action, $data);
							} else {
								return new Success("command", $action);
							}
						} else{
							return new Error('data', 'SESSION-ID-NOT-PROVIDED');
						}
					} else {
						return new Error('data', 'USER-NOT-EQUAL-TO-OC-USER');
					}
					break;
				}

		} else {
			return new Error($requestType, $action, "HTTP-TYPE-INVALID");
		}
	}
}
