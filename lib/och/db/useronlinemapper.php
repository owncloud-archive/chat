<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Mapper;
use \OCA\Chat\Core\Api;

class UserOnlineMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_users_online'); // tablename is news_feeds
    }
	
    public function getOnlineUsers(){
    	$result = $this->execute('SELECT * FROM `' . $this->getTableName() . '`');
    	
    	$rows = array();
    	
    	while($row = $result->fetchRow()){
    		array_push($rows, $row['user']);
    	}
    	
    	return $rows;
    }
    
    public function getAll(){
    	return $this->findEntities("SELECT * FROM " . $this->getTableName());
    }
	
	public function findByUser($user){
		$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
    			'WHERE `user` = ? ';
    	
    	$result = $this->execute($sql, array($user));
    	
  		$feeds = array();
        while($row = $result->fetchRow()){
        	$feed = new UserOnline();
            $feed->fromRow($row);
            array_push($feeds, $feed);
	    }

    	return $feeds;
	}

	public function deleteBySessionId($sessionID){
		$sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE `session_id` = ?';
		$this->execute($sql, array($sessionID));
	}
	
	public function updateLastOnline($sessionID, $timestamp){
		$sql = 'UPDATE `' . $this->getTableName() . '` SET `last_online` = ? WHERE `session_id` = ?';
		$this->execute($sql, array($timestamp, $sessionID));
	}
 
}
