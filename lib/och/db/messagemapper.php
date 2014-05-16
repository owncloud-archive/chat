<?php

namespace OCA\Chat\OCH\Db;

use OCA\Chat\Db\Mapper;

class MessageMapper extends Mapper{

    public function __construct(API $api) {
	parent::__construct($api, 'chat_och_messages'); // tablename is news_feeds
    }
 
    public function getMessagesByConvId($convId, $user){
        $sql = 'SELECT * FROM chat_och_messages WHERE `convid` = ? AND `timestamp` > (SELECT joined FROM chat_och_users_in_conversation WHERE `user` = ? )';
		return $this->findEntities($sql, array($convId, $user));
    }
    
}
