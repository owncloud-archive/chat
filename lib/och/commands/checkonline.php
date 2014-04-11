<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;

class CheckOnline extends ChatAPI {

    public function setRequestData(array $requestData){
        $this->requestData = $requestData;
    }

    public function execute(){	
        $mapper = $this->app['UserOnlineMapper'];
        $users = $mapper->getAll();
        foreach($users as $user){
            if((time() - $user->getLastOnline()) > 60){
                $mapper->deleteBySessionId($user->getSessionId());	
            }
        }
    }	
}
