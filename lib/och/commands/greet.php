<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnline;
use \OCA\Chat\OCH\Db\UserOnlineMapper;

class Greet extends ChatAPI {
	
    public function __construct(API $api){
        parent::__construct($api);
    }

    /*
     * @param $requestData['user'] String user id of the client
     * @param $requestData['session_id'] String session_id of the client
     * @param $requestData['timestamp'] Int timestamp when the command was send
    */
    public function setRequestData(array $requestData){
        $this->requestData = $requestData;
    }

    public function execute(){	
        $requestData = $this->getRequestData();
        $userOnline = new UserOnline();
        $userOnline->setUser($requestData['user']['backends']['och']['value']);
        $userOnline->setSessionId($requestData['session_id']);
        $userOnline->setLastOnline($requestData['timestamp']);
        $mapper = new UserOnlineMapper($this->api);
        $mapper->insert($userOnline);   		
        return;
    }	
}
