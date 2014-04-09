<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use OCA\Chat\OCH\Commands\SyncOnline;

class Online extends ChatAPI {
	
    public function __construct(API $api){
        parent::__construct($api);
    }


    public function setRequestData(array $requestData){
        $this->requestData = $requestData;
    }

    public function execute(){	
        $mapper = new UserOnlineMapper($this->api);
        $mapper->updateLastOnline($this->requestData['session_id'], $this->requestData['timestamp']);   		

        $syncOnline = new SyncOnline($this->api);
        $syncOnline->execute();
        return;
    }	
}
