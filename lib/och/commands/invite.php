<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCA\Chat\OCH\Db\User;

class Invite extends ChatAPI {
	
    /*
     * @param $requestData['user'] String user id of the client
     * @param $requestData['session_id'] String session_id of the client
     * @param $requestData['timestamp'] Int timestamp when the command was send
     * @param $requestData['conv_id'] String id of the conversation
     * @param $requestData['user_to_invite'] String id of the user which need to be invited
    */
    public function setRequestData(array $requestData){
    	
    	if(empty($requestData['conv_id'])){
            throw new RequestDataInvalid("CONV-ID-MUST-BE-PROVIDED");
        }

        if(empty($requestData['user_to_invite'])){
            throw new RequestDataInvalid("USER-TO-INVITE-MUST-BE-PROVIDED");	
        }

        if($requestData['user']['backends']['och']['value'] === $requestData['user_to_invite']['backends']['och']['value']){
            throw new RequestDataInvalid("USER-EQAUL-TO-USER-TO-INVITE");
        }
        
        if(!in_array($requestData['user_to_invite']['backends']['och']['value'], $this->app['API']->getUsers())){
            throw new RequestDataInvalid("USER-TO-INVITE-NOT-OC-USER");
        }

        $userOnlineMapper = $this->app['UserOnlineMapper'];
        $usersOnline = $userOnlineMapper->getOnlineUsers();
        if(!in_array($requestData['user_to_invite']['backends']['och']['value'], $usersOnline)){
            throw new RequestDataInvalid('USER-TO-INVITE-NOT-ONLINE');
        }

        $this->requestData = $requestData;
    }

    public function execute(){

        // We are going to add the user to the conv
        $userMapper = $this->app['UserMapper'];
        $user = new User();
        $user->setConversationId($this->requestData['conv_id']);
        $user->setJoined(time());
        $user->setUser($this->requestData['user_to_invite']['backends']['och']['value']);
        $userMapper->insertUnique($user);

        // First fetch every sessionID of the user to invite
        $userOnlineMapper = $this->app['UserOnlineMapper'];
        $pushMessageMapper = $this->app['PushMessageMapper'];

        $command = json_encode(array(
            "type" => "invite",
            "data" => array(
                "user" => $this->requestData['user'],
                "conv_id" => $this->requestData['conv_id'],
                "user_to_invite" => $this->requestData['user_to_invite']
            )
        ));

        $UTISession = $userOnlineMapper->findByUser($this->requestData['user_to_invite']['backends']['och']['value']); // $UTISessionID = UserToInviteSessionId = array()

        foreach($UTISession as $userToInvite){
            $pushMessage = new PushMessage();
	 	    $pushMessage->setSender($this->requestData['user']['backends']['och']['value']);
            $pushMessage->setReceiver($userToInvite->getUser());
            $pushMessage->setReceiverSessionId($userToInvite->getSessionId());
            $pushMessage->setCommand($command);
	   		$pushMessageMapper->insert($pushMessage);	
        }
        return;					
    }	
}
