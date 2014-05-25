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
		session_write_close();
		$request = json_decode($this->params('JSON'), true);
		list($requestType, $action, $httpType) = explode("::", $request['type']);

		if($httpType === "request"){
			// Check request type
			switch($requestType){
				case "command":
					// $action is the type of the command

					$possibleCommands = array('greet', 'join', 'invite', 'send_chat_msg', 'online', 'offline', 'start_conv', 'delete_init_conv');
					if(in_array($action, $possibleCommands)){
						if(!empty($request['data']['session_id'])){
							if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
								try{
									$commandClass = $this->app[$this->convertClassName($action) . 'Command'];
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
					$possibleCommands = array('get', 'delete');
					if(in_array($action, $possibleCommands)){
						if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
							if(!empty($request['data']['session_id'])){
								$pushClass = $this->app[$this->convertClassName($action) . 'Push'];
								$pushClass->setRequestData($request['data']);
								return $pushClass->execute();
							} else{
								return new Error('push', $action, 'SESSION-ID-NOT-PROVIDED');
							}
						} else {
							return new Error('push', $action, 'USER-NOT-EQUAL-TO-OC-USER');
						}
					} else {
						return new Error("command", $action, "PUSH-ACTION-NOT-FOUND");
					}
					break;
				case "data":
					$possibleCommands = array('messages', 'get_users');
					if(in_array($action, $possibleCommands)){
						if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
							if(!empty($request['data']['session_id'])){
								$dataClass = $this->app[$this->convertClassName($action) . 'Data'];
								$dataClass->setRequestData($request['data']);
								$data = $dataClass->execute();
								if($data){
									return new Success("command", $action, $data);
								} else {
									return new Success("command", $action);
								}
							} else{
								return new Error('data', $action, 'SESSION-ID-NOT-PROVIDED');
							}
						} else {
							return new Error('data', $action, 'USER-NOT-EQUAL-TO-OC-USER');
						}
					} else {
						return new Error("command", $action, "DATA-ACTION-NOT-FOUND");
					}
					break;
				}

		} else {
			return new Error($requestType, $action, "HTTP-TYPE-INVALID");
		}
	}

	private function convertClassName($class){
		$newClass = '';
		$parts = explode("_", $class);
		foreach($parts as $part){
			$newClass .= ucfirst($part);
		}
		return $newClass;
	}
}
