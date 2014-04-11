<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCA\Chat\OCH\Db\Message;
use OCA\Chat\OCH\Db\MessageMapper;

class SendChatMsg extends ChatAPI {
	
    public function setRequestData(array $requestData){
        if(empty($requestData['chat_msg'])){
            throw new RequestDataInvalid("CHAT-MSG-MUST-BE-PROVIDED");
        }
        if(empty($requestData['timestamp'])){
            throw new RequestDataInvalid("TIMESTAMP-MUST-BE-PROVIDED");
        }
        $this->requestData = $requestData;
    }

    public function execute(){
        $userMapper = $this->app['UserMapper'];
        $users = $userMapper->findByConversation($this->requestData['conv_id']);

        $command = json_encode(array(
            'type' => 'send_chat_msg',
            'data' => array(
                'user' => $this->requestData['user'], 
                'conv_id' => $this->requestData['conv_id'],
                'timestamp' => $this->requestData['timestamp'], 
                'chat_msg' => $this->requestData['chat_msg']
            )
        ));	

        $sender = $this->requestData['user']['backends']['och']['value']; 
        $PushMessageMapper = $this->app['PushMessageMapper'];

        foreach($users as $receiver){
            if($receiver->getUser() !== $sender){
                $pushMessage = $this->app['PushMessage'];
                $pushMessage->setSender($sender);
                $pushMessage->setReceiver($receiver->getUser());
                $pushMessage->setReceiverSessionId($receiver->getSessionId());
                $pushMessage->setCommand($command);
                $PushMessageMapper->insert($pushMessage);	
            }
        }
	
	// All done
	// insert this chatMsg into the messages table
	$messageMapper = $this->app['MessageMapper'];
	$message = $this->app['Message'];
	$message->setConvid($this->requestData['conv_id']);
	$message->setTimestamp($this->requestData['timestamp']);
	$message->setUser($this->requestData['user']['backends']['och']['value']);
	$message->setMessage($this->requestData['chat_msg']);
	$messageMapper->insert($message);
		
        return;
    }	
}
