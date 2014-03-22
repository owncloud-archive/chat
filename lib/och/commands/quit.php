<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Commands\Leave;

class Quit extends ChatAPI {
	
    public function __construct(API $api){
        parent::__construct($api);
    }

    public function setRequestData(array $requestData){
        $this->requestData = $requestData;
    }

    public function execute(){
        // First delete the sessionid from the online user table
        $userOnlineMapper = new UserOnlineMapper($this->api);
        $userOnlineMapper->deleteBySessionId($this->requestData['session_id']);

        // fetch all conversations where this sessionid joined

        $userMapper = new UserMapper($this->api);
        $conversations = $userMapper->findBySessionId($this->requestData['session_id']);

        foreach($conversations as $conversation){			
            // For each conversation, create a leave command and execute it
            $leave = new Leave($this->api, array('conversationID' => $conversation->getConversationId(), 'session_id' => $this->requestData['session_id']));
            $leave->execute();		
            // Left conversation 
        }
    }	
}



		
	