<?php

namespace OCA\Chat\OCH\Data;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use OCA\Chat\OCH\Db\MessageMapper;

class Messages extends ChatAPI {
	
    public function __construct(API $api){
        parent::__construct($api);
    }

    /*
     * @param $requestData['user'] String user id of the client
     * @param $requestData['convid'] String session_id of the client
    */
    public function setRequestData(array $requestData){
	// TODO check if $requestData['user'] is in the $requestData['conv'od] conv
        $this->requestData = $requestData;
    }

    public function execute(){	
	$return = array();
	$messageMapper = new MessageMapper($this->api);
	$msgs = $messageMapper->getMessagesByConvId($this->requestData['conv_id']);
	
	foreach($msgs as $msg){
	    $return[] = array(
		"id" => $msg->getId(),
		"convid" => $msg->getConvid(),
		"timestamp" => $msg->getTimestamp(),
		"user" => $msg->getUser(),
		"msg" => $msg->getMessage()
	    );
	}
	//var_dump($msgs);
	//throw new \Exception();
	return array("messages" => $return);
    }	
}
