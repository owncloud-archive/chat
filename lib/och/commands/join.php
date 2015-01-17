<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\Controller\OCH\ApiController;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\User;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCA\Chat\OCH\Db\InitConv;
use \OCA\Chat\OCH\Data\GetUsers;
use \OCA\Chat\App\Chat;


class Join extends ChatAPI {

	/**
	 * @var $pushMessageMapper \OCA\Chat\OCH\Db\PushMessageMapper
	 */
	private $pushMessageMapper;

	/**
	 * @var $getUsers \OCA\Chat\OCH\Data\GetUsers
	 */
	private $getUsers;

	/**
	 * @var $userMapper \OCA\Chat\OCH\Db\UserMapper
	 */
	private $userMapper;

	public function __construct(
		Chat $app,
		PushMessageMapper $pushMessageMapper,
		GetUsers $getUsers,
		UserMapper $userMapper
	){
		$this->app = $app;
		$this->pushMessageMapper = $pushMessageMapper;
		$this->getUsers = $getUsers;
		$this->userMapper  = $userMapper;
	}


	public function setRequestData(array $requestData){
		if(empty($requestData['conv_id'])){
			throw new RequestDataInvalid(ApiController::NO_CONV_ID);
		}
		$this->requestData = $requestData;
	}

	public function execute(){

		// Add the user to the conversation
		$user = new User();
		$user->setConversationId($this->requestData['conv_id']);
		$user->setJoined(time());
		$user->setUser($this->requestData['user']['id']);
		$this->userMapper->insertUnique($user);

		$this->getUsers->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$users = $this->getUsers->execute();
		$users = $users['users'];
		if(count($users) > 2){
			// we are in a group conv this mean we have to let the other users now we joined it
			$command = json_encode(array(
				"type" => "joined",
				"data" => array(
					"conv_id" => $this->requestData['conv_id'],
					"users" => $users
				)
			));
			$this->pushMessageMapper->createForAllUsersInConv(
				$this->requestData['user']['id'],
				$this->requestData['conv_id'],
				$command
			);
		}

		$this->getUsers->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$users = $this->getUsers->execute();

		return $users;
	}
}
