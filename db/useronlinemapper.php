<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;

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
}
