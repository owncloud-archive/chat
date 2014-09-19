<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\Conversation;
use \OCA\Chat\OCH\Db\ConversationMapper;
use \OCA\Chat\OCH\Commands\Join;
use \OCA\Chat\OCH\Commands\Invite;
use \OCA\Chat\OCH\Data\GetUsers;
use \OCA\Chat\OCH\Data\Messages;
use \OCA\Chat\OCH\Db\User;


class StartConv extends ChatAPI {

	public function setRequestData(array $requestData) {
		$this->requestData = $requestData;
	}

	public function execute(){

		// (1) generate a conv id
		$ids = array();
		foreach($this->requestData['user_to_invite'] as $userToInvite){
			$ids[] = $userToInvite['backends']['och']['value'];
		}
		// always add our selve to the array for the conv id
		$ids[] = $this->requestData['user']['backends']['och']['value'];

		// (2) check if conv id exists
		$convMapper = $this->c['ConversationMapper'];

		if($id = $convMapper->existsByUsers($ids)){
			$id = $id['conv_id'];
		} else {
			$id = $this->generateConvId();

			// (3) Create the conv
			$conversation = new Conversation();
			$conversation->setConversationId($id);
			$mapper = $this->c['ConversationMapper'];
			$mapper->insert($conversation);
		}

		// (5) invite the user_to_invite since we just created the conv
		// foreach user to invite
		$invite = $this->c['InviteCommand'];
		$requestData = array();
		$requestData['conv_id'] = $id;
		$requestData['user'] = $this->requestData['user'];
		foreach($this->requestData['user_to_invite'] as $userToInvite){
			if($userToInvite['backends']['och']['value'] !== $this->requestData['user']['backends']['och']['value']){
				$requestData['user_to_invite'] = $userToInvite;
				$invite->setRequestData($requestData);
				$invite->execute();
			}
		}

		// (4) join the just created conv
		$join = $this->c['JoinCommand'];
		$this->requestData['conv_id'] = $id;
		$join->setRequestData($this->requestData);
		$join->execute();

		// Fetch users in conv
		$getUsers = $this->c['GetUsersData'];
		$getUsers->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$users = $getUsers->execute();
		$users = $users['users'];

		// Fetch messages in conv
		$getMessages = $this->c['MessagesData'];
		$getMessages->setRequestData(array(
			"conv_id" => $this->requestData['conv_id'],
			'user' => $this->requestData['user']
		));
		$messages = $getMessages->execute();
		$messages = $messages['messages'];

		return array(
			"conv_id" => $id,
			"users" => $users,
			"messages" => $messages
		);
	}

	private function generateConvId(){
		$convMapper = $this->c['ConversationMapper'];
		$id = 'CONV_ID_' . time() . '_' . rand(1, 99);
		if($convMapper->existsByConvId($id)){
			return $this->generateConvId();
		} else {
			return $id;
		}
	}
}
