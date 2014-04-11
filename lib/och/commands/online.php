<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Commands\CheckOnline;

class Online extends ChatAPI {
	
    public function __construct(API $api){
        parent::__construct($api);
    }


    public function setRequestData(array $requestData){
        $this->requestData = $requestData;
    }

    public function execute(){	
        $mapper = $this->app['UserOnlineMapper'];
        $mapper->updateLastOnline($this->requestData['session_id'], $this->requestData['timestamp']);   		

        $checkOnline = new CheckOnline($this->api);
        $checkOnline->execute();
        return;
    }	
}
