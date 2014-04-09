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
	
    public function __construct(API $api){
        parent::__construct($api);
    }

    public function setRequestData(array $requestData){
        $this->requestData = $requestData;
    }

    public function execute(){	
        $mapper = new UserOnlineMapper($this->api);
        $users = $mapper->getAll();
        foreach($users as $user){
            if((time() - $user->getLastOnline()) > 60){
		\OCP\Util::writeLog('chat', 'Deleting offline user in SyncOnline add ' . time() . ' with session_id ' 
		    . $user->getSessionId() 
		    . ' and username ' . $user->getUser() 
		    . ' which was last online at ' . $user->getLastOnline(), \OCP\Util::ERROR);
                $mapper->deleteBySessionId($user->getSessionId());	
            }
        }
    }	
}
