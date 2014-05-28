<?php

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;

class MessageMapper extends Mapper{

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_och_messages'); // tablename is news_feeds
	}
 
	public function getMessagesByConvId($convId, $user){
		$sql = <<<SQL
			SELECT
				*
			FROM
				*PREFIX*chat_och_messages
			WHERE
				`convid` = ?
			AND
				`timestamp` > (
					SELECT
						joined
					FROM
						*PREFIX*chat_och_users_in_conversation
					WHERE
						`user` = ?
					AND `conversation_id` = ?
				)
SQL;

		return $this->findEntities($sql, array($convId, $user, $convId));
	}

}
