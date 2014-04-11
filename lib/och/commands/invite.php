<?php

namespace OCA\Chat\OCH\Commands;

use \OCA\Chat\OCH\ChatAPI;
use \OCA\Chat\Core\API;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\PushMessageMapper;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;

class Invite extends ChatAPI {
	
    public function __construct(API $api){
            parent::__construct($api);
    }

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

        if($requestData['user'] === $requestData['user_to_invite']){
            throw new RequestDataInvalid("USER-EQAUL-TO-USER-TO-INVITE");
        }

        if(!in_array($requestData['user_to_invite']['backends']['och']['value'], \OCP\User::getUsers())){
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

        $UTISessionID = $userOnlineMapper->findByUser($this->requestData['user_to_invite']['backends']['och']['value']); // $UTISessionID = UserToInviteSessionId = array()

        foreach($UTISessionID as $userToInvite){
        //    $pushMessage = $this->app['PushMessage'];
          $pushMessage = new PushMessage();
	    $pushMessage->setSender($this->requestData['user']['backends']['och']['value']);
            $pushMessage->setReceiver($userToInvite->getUser());
            $pushMessage->setReceiverSessionId($userToInvite->getSessionId());
            $pushMessage->setCommand($command);
	    echo $pushMessage->id;
	    $pushMessageMapper->insert($pushMessage);	
        }
        return;					
    }	
}
