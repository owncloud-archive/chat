<?php

namespace OCA\Chat\OCH\Push;

use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\Db\DoesNotExistException;
use \OCP\AppFramework\Http\JSONResponse;

class Get extends ChatAPI{

    public function __construct(API $api){
        parent::__construct($api);
    }

    public function setRequestData(array $requestData){
        $this->requestData = $requestData;
    }

    public function execute(){
        session_write_close();
        try {
            $mapper = new PushMessageMapper($this->api); // inject API class for db access
            $this->pushMessages = $mapper->findBysSessionId($this->requestData['session_id']);  
        } catch(DoesNotExistException $e){
            sleep(1);
            $this->execute();
        }
    
        $commands = array();
        foreach($this->pushMessages as $pushMessage){
            $command = json_decode($pushMessage->getCommand(), true);
            $commands[$pushMessage->getId()] = $command;
        }
        return new JSONResponse(array('push_msgs' => $commands));
    }	
}
