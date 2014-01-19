<?php

namespace OCA\Chat\Push;
use \OCA\Chat\Commands\Command;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\ChatAPI;
use \OCA\Chat\Exceptions\RequestDataInvalid;

use \OCA\Chat\Db\PushMessageMapper;
use \OCA\Chat\Db\PushMessage;

use \OCA\AppFramework\Http\JSONResponse;

use \OCA\AppFramework\Db\DoesNotExistException;

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
