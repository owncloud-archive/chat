<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;
use \OCA\AppFramework\Core\Api;


class PushMessageMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_push_messages'); // tablename is news_feeds
    }

	public function findBysSessionId($sessionId){
  		$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' . 'WHERE `receiver_session_id` = ? ORDER BY id LIMIT 1';
		return $this->findEntity($sql, array($sessionId));
	}
	
		
}