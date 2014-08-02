<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

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

	const INVALID_HTTP_TYPE = 0;
	const COMMAND_NOT_FOUND = 1;
	const PUSH_ACTION_NOT_FOUND = 2;
	const DATA_ACTION_NOT_FOUND = 3;
	const NO_SESSION_ID = 6;
	const USER_NOT_EQUAL_TO_OC_USER = 7;
	const NO_TIMESTAMP = 8;
	const NO_CONV_ID = 9;
	const NO_USER_TO_INVITE = 10;
	const USER_EQUAL_TO_USER_TO_INVITE = 11;
	const USER_TO_INVITE_NOT_OC_USER = 12;
	const NO_CHAT_MSG = 13;

	public function __construct(API $api, IRequest $request, IAppContainer $app){
		parent::__construct($api->getAppName(), $request);
		$this->app = $app;
	}

	/**
	 * Routes the API Request
	 * @param string $type
	 * @param array $data
	 * @return JSONResponse
	 * @NoAdminRequired
	 */
	public function route($type, $data){
		session_write_close();
		list($requestType, $action, $httpType) = explode("::", $type);

		if($httpType === "request"){
			// Check request type
			switch($requestType){
				case "command":
					// $action is the type of the command

					$possibleCommands = array('greet', 'join', 'invite', 'send_chat_msg', 'online', 'offline', 'start_conv', 'delete_init_conv', 'archive', 'un_archive');
					if(in_array($action, $possibleCommands)){
						if(!empty($data['session_id'])){
							if($data['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
								try{
									$commandClass = $this->app[$this->convertClassName($action) . 'Command'];
									$commandClass->setRequestData($data);
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
								return new Error("command", $action, self::USER_NOT_EQUAL_TO_OC_USER);
							}
						} else {
							return new Error("command", $action,  self::NO_SESSION_ID);
						}
					} else {
						return new Error("command", $action, self::COMMAND_NOT_FOUND);
					}

					break;
				case "push":
					$possibleCommands = array('get', 'delete');
					if(in_array($action, $possibleCommands)){
						if($data['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
							if(!empty($data['session_id'])){
								$pushClass = $this->app[$this->convertClassName($action) . 'Push'];
								$pushClass->setRequestData($data);
								return $pushClass->execute();
							} else{
								return new Error('push', $action, self::NO_SESSION_ID);
							}
						} else {
							return new Error('push', $action, self::USER_NOT_EQUAL_TO_OC_USER);
						}
					} else {
						return new Error("command", $action, self::PUSH_ACTION_NOT_FOUND);
					}
					break;
				case "data":
					$possibleCommands = array('messages', 'get_users');
					if(in_array($action, $possibleCommands)){
						if($data['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
							if(!empty($data['session_id'])){
								$dataClass = $this->app[$this->convertClassName($action) . 'Data'];
								$dataClass->setRequestData($data);
								$data = $dataClass->execute();
								if($data){
									return new Success("command", $action, $data);
								} else {
									return new Success("command", $action);
								}
							} else{
								return new Error('data', $action, self::NO_SESSION_ID);
							}
						} else {
							return new Error('data', $action, self::USER_NOT_EQUAL_TO_OC_USER);
						}
					} else {
						return new Error("command", $action, self::DATA_ACTION_NOT_FOUND);
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
