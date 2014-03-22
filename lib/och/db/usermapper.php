<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Mapper;
use \OCA\Chat\Core\Api;

class UserMapper extends Mapper {


    public function __construct(API $api) {
        parent::__construct($api, 'chat_och_users_in_conversation'); // tablename is news_feeds
    }

    public function findByConversation($conversationId){
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
                        'WHERE `conversation_id` = ? ';

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
		
    public function findBySessionId($sessionID){
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
    			'WHERE `session_id` = ? ';
    	
    	$result = $this->execute($sql, array($sessionID));
    	
        $feeds = array();
        while($row = $result->fetchRow()){
            $feed = new User();
            $feed->fromRow($row);
            array_push($feeds, $feed);
        }

    	return $feeds;
    }
	
    public function deleteBySessionId($conversationID, $sessionID){
        $sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE `conversation_id` = ? AND `session_id` = ?';
        $this->execute($sql, array($conversationID, $sessionID));
    }
	
}