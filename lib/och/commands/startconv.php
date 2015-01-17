<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\Conversation;
use \OCA\Chat\OCH\Db\MessageMapper;
use \OCA\Chat\OCH\Db\ConversationMapper;
use \OCA\Chat\OCH\Data\GetUsers;
use \OCA\Chat\OCH\Data\Messages;


class StartConv extends ChatAPI {

	/**
	 * @var $messageMapper \OCA\Chat\OCH\Db\messageMapper
	 */
	private $messageMapper;

	/**
	 * @var $conversationMapper \OCA\Chat\OCH\Db\ConversationMapper
	 */
	private $conversationMapper;

	/**
	 * @var $invite \OCA\Chat\OCH\Commands\Invite;
	 */
	private $invite;

	/**
	 * @var $join \OCA\Chat\OCH\Commands\Join;
	 */
	private $join;

	/**
	 * @var $getUsers \OCA\Chat\OCH\Data\GetUsers
	 */
	private $getUsers;

	/**
	 * @var $messages \OCA\Chat\OCH\Data\Messages
	 */
	private $messages;

	public function __construct(
		MessageMapper $messageMapper,
		ConversationMapper $conversationMapper,
		Invite $invite,
		Join $join,
		GetUsers $getUsers,
		Messages $messages
	){
		$this->messageMapper = $messageMapper;
		$this->conversationMapper = $conversationMapper;
		$this->invite = $invite;
		$this->join = $join;
		$this->getUsers = $getUsers;
		$this->messages = $messages;
	}


	public function setRequestData(array $requestData) {
		$this->requestData = $requestData;
	}

	public function execute(){

		// (1) generate a conv id
		$ids = array();
		foreach($this->requestData['user_to_invite'] as $userToInvite){
			$ids[] = $userToInvite['id'];
		}
		// always add our selve to the array for the conv id
		$ids[] = $this->requestData['user']['id'];

		// (2) check if conv id exists

		if($id = $this->conversationMapper->existsByUsers($ids)){
			$id = $id['conv_id'];
		} else {
			$id = $this->generateConvId();

			// (3) Create the conv
			$conversation = new Conversation();
			$conversation->setConversationId($id);
			$this->conversationMapper->insert($conversation);
		}

		// (5) invite the user_to_invite since we just created the conv
		// foreach user to invite
		$requestData = array();
		$requestData['conv_id'] = $id;
		$requestData['user'] = $this->requestData['user'];
		foreach($this->requestData['user_to_invite'] as $userToInvite){
			if($userToInvite['id'] !== $this->requestData['user']['id']){
				$requestData['user_to_invite'] = $userToInvite;
				$this->invite->setRequestData($requestData);
				$this->invite->execute();
			}
		}

		// (4) join the just created conv
		$this->requestData['conv_id'] = $id;
		$this->join->setRequestData($this->requestData);
		$this->join->execute();

		// Fetch users in conv
		$this->getUsers->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$users = $this->getUsers->execute();
		$users = $users['users'];

		// Fetch messages in conv
		$this->messages->setRequestData(array(
			"conv_id" => $this->requestData['conv_id'],
			'user' => $this->requestData['user']
		));
		$messages = $this->messages->execute();
		$messages = $messages['messages'];

		return array(
			"conv_id" => $id,
			"users" => $users,
			"messages" => $messages
		);
	}

	private function generateConvId(){
		$id = 'CONV_ID_' . time() . '_' . rand(1, 99);
		if($this->conversationMapper->existsByConvId($id)){
			return $this->generateConvId();
		} else {
			return $id;
		}
	}
}
