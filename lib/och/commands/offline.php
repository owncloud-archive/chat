<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Commands\SyncOnline;

class Offline extends ChatAPI {


    public function setRequestData(array $requestData){
        $this->requestData = $requestData;
    }

    public function execute(){	
        $mapper = $this->app['UserOnlineMapper'];
        $mapper->deleteBySessionId($this->requestData['session_id']);   		
	
		$syncOnline = new SyncOnline($this->app);
        $syncOnline->execute();
        
        // we have to "leave" every conversation we joined in this sessionid
        // fetch every conv we joined
        $userMapper = $this->app['UserMapper'];
        $convs = $userMapper->findBySessionId($this->requestData['session_id']);
        foreach($convs as $conv){
        	$userMapper->deleteBySessionId($conv->getConversationId(), $this->requestData['session_id']);
        }
        
		return;
    }	
}
