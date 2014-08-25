<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use OCA\Chat\Controller\OCH\ApiController;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCA\Chat\OCH\Db\User;

class Invite extends ChatAPI {
	
	/*
	 * @param $requestData['user'] String user id of the client
	 * @param $requestData['session_id'] String session_id of the client
	 * @param $requestData['timestamp'] Int timestamp when the command was send
	 * @param $requestData['conv_id'] String id of the conversation
	 * @param $requestData['user_to_invite'] String id of the user which need to be invited
	*/
	public function setRequestData(array $requestData){

		if(empty($requestData['conv_id'])){
			throw new RequestDataInvalid(ApiController::NO_SESSION_ID);
		}

		if(empty($requestData['user_to_invite'])){
			throw new RequestDataInvalid(ApiController::NO_USER_TO_INVITE);
		}

		if($requestData['user']['backends']['och']['value'] === $requestData['user_to_invite']['backends']['och']['value']){
			throw new RequestDataInvalid(ApiController::USER_EQUAL_TO_USER_TO_INVITE);
		}

		if(!in_array($requestData['user_to_invite']['backends']['och']['value'], \OCP\User::getUsers())){
			throw new RequestDataInvalid(ApiController::USER_TO_INVITE_NOT_OC_USER);
		}

		$this->requestData = $requestData;
	}

	public function execute(){

		// add the user to thx	e conv
		// this is done by executing the join command
		$join = $this->c['JoinCommand'];
		$requestData = array(
			"user" => $this->requestData['user_to_invite'],
			"conv_id" => $this->requestData['conv_id']
		);
		$join->setRequestData($requestData);
		$join->execute();

		$command = json_encode(array(
			"type" => "invite",
			"data" => array(
				"user" => $this->requestData['user'],
				"conv_id" => $this->requestData['conv_id'],
				"user_to_invite" => $this->requestData['user_to_invite']
			)
		));

		$pushMessageMapper = $this->c['PushMessageMapper'];
		$pushMessageMapper->createForAllSessionsOfAUser(
			$this->requestData['user_to_invite']['backends']['och']['value'],
			$this->requestData['user']['backends']['och']['value'],
			$command
		);

		$getUsers = $this->c['GetUsersData'];
		$getUsers->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$users = $getUsers->execute();
		$users = $users['users'];

		return array(
			"users" => $users
		);
	}
}
