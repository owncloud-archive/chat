<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;
use \OCA\AppFramework\Core\Api;


class ConversationMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_conversations'); // tablename is news_feeds
      $this->tableName = '*PREFIX*' . 'chat_conversations';
    }

	public function updateName($conversation){
		$userMapper = new UserMapper($this->api);
		$users = $userMapper->findByConversation($conversation);
		
		$usersArray = array();
		foreach($users as $user){
			array_push($usersArray, $user->getUser());	
		}
		$name = json_encode($usersArray);
		$sql = 'UPDATE `' . $this->tableName . '` SET name=?, generated=1 WHERE `conversation_id` = ?';
		$this->execute($sql, array($name, $conversation)); //$sql, array $params=array(), $limit=null, $offset=nul			
	}
	
	public function deleteConversation($conversationID){
		$sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE `conversation_id` = ? ';
        $this->execute($sql, array($conversationID));
	}
	
	public function findByConversationId($conversationID){
     	$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' . 'WHERE `conversation_id` = ?';
  		return $this->findEntity($sql, array($conversationID));
	}
	
}