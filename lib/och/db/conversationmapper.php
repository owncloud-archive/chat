<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Mapper;
use \OCA\Chat\Core\API;
use OCA\Chat\Db\DoesNotExistException;

class ConversationMapper extends Mapper {

    public function __construct(API $api) {
		parent::__construct($api, 'chat_och_conversations'); // tablename is news_feeds
    }

    public function deleteConversation($conversationID){
        $sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE `conversation_id` = ? ';
        $this->execute($sql, array($conversationID));
    }

    public function findByConversationId($conversationID){
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` ' . 'WHERE `conversation_id` = ?';
        return $this->findEntity($sql, array($conversationID));
    }

	public function existsByConvId($id){
		$sql = 'SELECT conversation_id FROM `' . $this->getTableName() . '` ' . 'WHERE `conversation_id` = ?';
		$result = $this->execute($sql, array($id));
		if(count($result->fetchAll()) === 1){
			return true;
		} else {
			return false;
		}
	}

	public function existsByUsers($users){
		$sql = " SELECT DISTINCT c1.conversation_id AS conv_id"
			 . " FROM   chat_och_users_in_conversation    c1"
			 . " WHERE EXISTS ("
				. " SELECT 1 FROM chat_och_users_in_conversation  c2"
				. " WHERE c1.conversation_id = c2.conversation_id"
				. " AND   c2.user = ?"
			. " )";
		for($i = 0; $i < (count($users) -1); $i++){
			$sql .= " AND EXISTS ("
				  	. " SELECT 1 FROM chat_och_users_in_conversation  c2"
					. " WHERE c1.conversation_id = c2.conversation_id"
					. " AND   c2.user = ? "
				    . " )";
		}

		$sql .= " AND NOT EXISTS ("
			. " SELECT 1 FROM chat_och_users_in_conversation  c2"
				. " WHERE c1.conversation_id = c2.conversation_id"
			. " AND   c2.user NOT IN (";
			foreach($users as $key=>$user){
				if($key === (count($users)-1)){
					$sql .= " ?";
				} else {
					$sql .= " ?,";
				}
			}
		$sql .=")"
		. " )";

		$params = array();

		foreach($users as $user){
			$params[] = $user;
		}

		foreach($users as $user){
			$params[] = $user;
		}

//		echo "<pre>";
//		var_dump($sql);
//		var_dump($params);

		try{
            $result = $this->execute($sql, $params);
			$row = $result->fetchRow();
			return $row;

		} catch (DoesNotExistException $exception) {
			var_dump($exception);
            return false;
        }
    }	
}