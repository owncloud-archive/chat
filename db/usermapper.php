<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;

class UserMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_users_in_conversation'); // tablename is news_feeds
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
	
 
}