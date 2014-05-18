<?php

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
		$id = $this->generateConvId($ids);

		// (2) check if conv id exists
		$convMapper = $this->app['ConversationMapper'];
		if($convMapper->exists($id)){

			// (3) join the already existing conv
			$join = $this->app['JoinCommand'];
			$this->requestData['conv_id'] = $id;
			$join->setRequestData($this->requestData);
			$join->execute();

		} else {

			// (3) Create the conv
			$conversation = new Conversation();
			$conversation->setConversationId($id);
			$mapper = $this->app['ConversationMapper'];
			$mapper->insert($conversation);

			// (4) join the just created conv
			$join = $this->app['JoinCommand'];
			$this->requestData['conv_id'] = $id;
			$join->setRequestData($this->requestData);
			$join->execute();

		}

		// (5) invite the user_to_invite since we just created the conv
		// foreach user to invite
		$invite = $this->app['InviteCommand'];
		$reuqestData = array();
		$requestData['conv_id'] = $id;
		$requestData['user'] = $this->requestData['user'];
		foreach($this->requestData['user_to_invite'] as $userToInvite){
			if($userToInvite['backends']['och']['value'] !== $this->requestData['user']['backends']['och']['value']){
				$requestData['user_to_invite'] = $userToInvite;
				$invite->setRequestData($requestData);
				$invite->execute();
			}
		}

		// (6) add our selv to the conv
		// the other users will be added in the invite command
		$userMapper = $this->app['UserMapper'];
		$user = new User();
		$user->setConversationId($this->requestData['conv_id']);
		$user->setJoined(time());
		$user->setUser($this->requestData['user']['backends']['och']['value']);
		$userMapper->insertUnique($user);


		// Fetch users in conv
		$getUsers = $this->app['GetUsersData'];
		$getUsers->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$users = $getUsers->execute();
		$users = $users['users'];

		// Fetch messages in conv
		$getMessages = new Messages($this->app);
		$getMessages->setRequestData(array("conv_id" => $this->requestData['conv_id']));
		$messages = $getMessages->execute();
		$messages = $messages['messages'];

		return array("conv_id" => $id,
					 "users" => $users,
					 "messages" => $messages
		);
	}

	private function generateConvId($users){

		$id = '';
		foreach($users as $user){
			$id .= $user;
		}

		$id = str_split($id);
		sort($id);
		$id = implode($id);

		return $id;

	}

}
