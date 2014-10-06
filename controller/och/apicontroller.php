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
use OCA\Chat\Db\DBException;
use OCA\Chat\App\Chat;

class ApiController extends Controller {

	/**
	 * Error codes used by the API
	 */
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
	const NO_USER = 14;

	public function __construct($appName, IRequest $request,  Chat $app){
		parent::__construct($appName, $request);
		$this->app = $app;
		$this->c = $app->getContainer();
	}

	/**
	 * Routes the API Request to the correct Class
	 * There are 3 types: command, data and push
	 * @param string $type
	 * @param array $data
	 * @return JSONResponse
	 * @NoAdminRequired
	 */
	public function route($type, $data){
		session_write_close();
		list($requestType, $action, $httpType) = explode("::", $type);

		if($httpType === "request"){
			if(!empty($data['session_id'])){
				if(!empty($data['user'])){
					if($data['user']['backends']['och']['value'] === $this->c['UserSession']->getUser()->getUID()){
						try{
							switch($requestType){
								case "command":
									$possibleCommands = array('greet', 'join', 'invite', 'send_chat_msg', 'online', 'offline', 'start_conv', 'delete_init_conv', 'attach_file', 'remove_file');
									if(in_array($action, $possibleCommands)){
										$commandClass = $this->c[$this->convertClassName($action) . 'Command'];
										$commandClass->setRequestData($data);
										$data = $commandClass->execute();
										if($data){
											return new Success("command", $action, $data);
										} else {
											return new Success("command", $action);
										}
									} else {
										return new Error("command", $action, self::COMMAND_NOT_FOUND);
									}
									break;
								case "push":
									$possibleCommands = array('get', 'delete');
									if(in_array($action, $possibleCommands)){
										$pushClass = $this->c[$this->convertClassName($action) . 'Push'];
										$pushClass->setRequestData($data);
										return $pushClass->execute();
									} else {
										return new Error("push", $action, self::PUSH_ACTION_NOT_FOUND);
									}
									break;
								case "data":
									$possibleCommands = array('messages', 'get_users');
									if(in_array($action, $possibleCommands)){
										$dataClass = $this->c[$this->convertClassName($action) . 'Data'];
										$dataClass->setRequestData($data);
										$data = $dataClass->execute();
										if($data){
											return new Success("data", $action, $data);
										} else {
											return new Success("data", $action);
										}
									} else {
										return new Error("data", $action, self::DATA_ACTION_NOT_FOUND);
									}
									break;
							}
						}catch(DBException $e){
							return new Error($requestType, $action, "ERROR::DB::" . $e->getMessage());
						}
						catch(RequestDataInvalid $e){
							return new Error($requestType, $action, $e->getMessage());
						}
					} else {
						return new Error($requestType, $action, self::USER_NOT_EQUAL_TO_OC_USER);
					}
				} else {
					return new Error($requestType, $action,  self::NO_USER);
				}
			} else {
				return new Error($requestType, $action,  self::NO_SESSION_ID);
			}
		} else {
			return new Error($requestType, $action, self::INVALID_HTTP_TYPE);
		}
	}

	/**
	 * Helper function to transform the $action to a correct classname
	 * @param $class
	 * @return string
	 */
	private function convertClassName($class){
		$newClass = '';
		$parts = explode("_", $class);
		foreach($parts as $part){
			$newClass .= ucfirst($part);
		}
		return $newClass;
	}
}
