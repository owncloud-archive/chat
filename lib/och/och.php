<?php

namespace OCA\Chat\OCH;

use \OCA\Chat\IBackend;
use \OCA\Chat\App\Chat;
use \OCA\Chat\AbstractBackend;

class OCH extends AbstractBackend implements IBackend {

	public function getId(){
		return 'och';
	}

	public function getInitConvs(){
		if(count(self::$initConvs) === 0){
			$this->createInitConvs();
		}
		return self::$initConvs;
	}

	private function createInitConvs(){
		$initConvs = array();
		$userMapper = $this->c['UserMapper'];
		$convs = $userMapper->findByUser($this->app->getUserId());
		$usersAllreadyInConv = array();
		$join = $this->c['JoinCommand'];
		foreach($convs as $conv){
			$users = $userMapper->findUsersInConv($conv->getConversationId());
			// Find the correct contact for the correct user
			$getMessages = $this->c['MessagesData'];
			$getMessages->setRequestData(array(
				"conv_id" => $conv->getConversationId(),
				'user' => $this->app->getCurrentUser()
			));
			$messages = $getMessages->execute();
			$messages = $messages['messages'];

			$attachmentMapper = $this->c['AttachmentMapper'];
			$files = $attachmentMapper->findRawByConv($conv->getConversationId());
			$initConvs[$conv->getConversationId()] = array(
				"id" => $conv->getConversationId(),
				"users"=> $users,
				"backend" => "och",
				"messages" => $messages,
				"files" => $files
			);
			if(count($users) === 2){
				foreach($users as $user){
					if($user !== \OCP\User::getUser()){
						$usersAllreadyInConv[] = $user;
					}
				}
			}

			$join->setRequestData(array(
				"conv_id" => $conv->getConversationId(),
				"user" => $this->app->getCurrentUser(),
			));
			$join->execute();
		}

		$allUsers = \OCP\User::getUsers();
		$users = array_diff($allUsers, $usersAllreadyInConv);

		$startConv = $this->c['StartConvCommand'];
		foreach($users as $user){
			if($user !== \OCP\User::getUser()){
				$startConv->setRequestData(array(
					"user" => $this->app->getCurrentUser(),
					"user_to_invite" => array(
						$this->app->getUserasContact($user),
					)
				));
				$info =  $startConv->execute();
				$initConvs[$info['conv_id']] = array(
					"id" => $info['conv_id'],
					"users"=> array(
						\OCP\User::getUser(),
						$user
					),
					"backend" => "och",
					"messages" => array()
				);

			}
		}
		self::$initConvs = $initConvs;
	}

	public function getDisplayName(){
		return 'ownCloud Chat';
	}

	public function getProtocols(){
		return array('x-owncloud-handle');
	}

}
