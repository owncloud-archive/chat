<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;

// This is the redunant online/offline system
// because sometiems the onbeforeunload doens't work we are going to check
// if the current timestamp minus the lastonline of every sessionid
// (aka the time between now and the last time that the user was online)
// is greater than 60 -> if so make the user offline

class SyncOnline extends ChatAPI {

	public function setRequestData(array $requestData){
		$this->requestData = $requestData;
	}

	public function execute(){

		$mapper = $this->app['UserOnlineMapper'];
		$users = $mapper->getAll();

		foreach($users as $user){
			if((time() - $user->getLastOnline()) > 70){
				
				$this->app['API']->log('chat', 'Deleting offline user in SyncOnline add ' . time() . ' with session_id '
				. $user->getSessionId()
				. ' and username ' . $user->getUser()
				. ' which was last online at ' . $user->getLastOnline(), \OCP\Util::ERROR);
				$mapper->deleteBySessionId($user->getSessionId());
			}
		}
	}
}
