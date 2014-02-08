<?php
//broken
namespace OCA\Chat\OCH\Commands;

use \OCA\AppFramework\Core\API;
use \OCA\Chat\OCH\Commands\Command;
use \OCA\Chat\OCH\Db\User;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\OCH\Db\ConversationMapper;
use \OCA\Chat\OCH\Db\Conversation;

class GetConversations extends Command {
	
	public function __construct(API $api, $params){
		parent::__construct($api, $params);
	}
	
	public function execute(){
		$userMapper = new UserMapper($this->api);
		$conversationsDB = $userMapper->findByUser($this->params('user'));
		$conversations = array();
		foreach($conversationsDB as $conversationDB){
   			$conversationMapper = new ConversationMapper($this->api); 
			$conversation = array("conversationID" => $conversationDB->getConversationId());
			array_push($conversations, $conversation);
		}
		return $conversations;
	}	

}
