<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Mapper;
use \OCA\Chat\Core\Api;
use \OCA\Chat\Db\Entity;

class UserMapper extends Mapper {
	
	private $userOnlineTable = '*PREFIX*chat_och_users_online';

    public function __construct(API $api) {
        parent::__construct($api, 'chat_och_users_in_conversation'); // tablename is news_feeds
    }

    public function findSessionsByConversation($conversationId){
    	$sql = 'SELECT ' . $this->getTableName() . '.user,
      			' . $this->userOnlineTable . '.session_id '
    	     . ' FROM ' . $this->getTableName() . ' INNER JOIN ' . $this->userOnlineTable
    	     . ' ON ' . $this->getTableName() . '.user = ' . $this->userOnlineTable . '.user '
    	     . ' AND ' . $this->getTableName() . '.conversation_id = ? ';
        
    	$result = $this->execute($sql, array($conversationId));

        $feeds = array();
        while($row = $result->fetchRow()){
            $feed = new User();
            $feed->fromRow($row);
            array_push($feeds, $feed);
        }

        return $feeds;
    }

    public function findByUser($user){
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
    			'WHERE `user` = ? ';
    	
    	$result = $this->execute($sql, array($user));
    	
        $feeds = array();
        while($row = $result->fetchRow()){
            $feed = new User();
            $feed->fromRow($row);
            array_push($feeds, $feed);
        }

    	return $feeds;
    }
	
     public function findConvsIdByUser($user){
        $sql = 'SELECT conversation_id FROM `' . $this->getTableName() . '` ' .
    			'WHERE `user` = ? ';
    	
    	$result = $this->execute($sql, array($user));
       
        $ids = array();
        while($row = $result->fetchRow()){
            array_push($ids, $row['conversation_id']);
        }
       
        $ids = array_unique($ids);
        return $ids;
    }
		
    
    public function findUsersInConv($id){
        $sql = 'SELECT user FROM `' . $this->getTableName() . '` ' .
    			'WHERE `conversation_id` = ? ';
    	
    	$result = $this->execute($sql, array($id));
       
        $users = array();
        while($row = $result->fetchRow()){
            array_push($users, $row['user']);
        }
       
        $users = array_unique($users);
        return $users;
    }
    
    public function insertUnique(Entity $entity){
    	$sql = 'INSERT INTO ' . $this->getTableName() 
			 . ' SELECT * FROM (SELECT ?,?) AS tmp
				WHERE NOT EXISTS (
					SELECT  conversation_id, `user` FROM `' . $this->getTableName() .'` WHERE conversation_id = ? AND `user` = ?
				) LIMIT 1';

    	$this->execute($sql, array(
    			$entity->getConversationId(),
    			$entity->getUser(),
    			$entity->getConversationId(),
    			$entity->getUser(),
    	));
    	 
    }
	
}