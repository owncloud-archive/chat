<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Mapper;
use \OCA\Chat\Core\Api;
use \OCA\Chat\Db\DoesNotExistException;

class PushMessageMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_och_push_messages'); // tablename is news_feeds
    }

	public function findBysSessionId($sessionId){
  		$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' . 'WHERE `receiver_session_id` = ?';
		$feeds =  $this->findEntities($sql, array($sessionId));
		if (count($feeds) === 0 ){
			throw new DoesNotExistException('');
		} else {		
			return $feeds;
		}
	}
	
		
}