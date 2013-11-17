<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;
use \OCA\AppFramework\Core\Api;
use \OCA\Appframework\Db\DoesNotExistException;

class PushMessageMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_push_messages'); // tablename is news_feeds
    }

	public function findBysSessionId($sessionId){
  		$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' . 'WHERE `receiver_session_id` = ? ORDER BY id';
		$feeds =  $this->findEntities($sql, array($sessionId));
		if (count($feeds) === 0 ){
			throw new DoesNotExistException('');
		} else {		
			return $feeds;
		}
	}
	
		
}