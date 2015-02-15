<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;

class MessageMapper extends Mapper{

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_och_messages'); // tablename is news_feeds
	}
 
	public function getMessagesByConvId($convId, $user, $startpoint=0){
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
						`joined`
					FROM
						*PREFIX*chat_och_users_in_conversation
					WHERE
						`user` = ?
					AND `conversation_id` = ?
				)
SQL;
		if($startpoint !== 0){
			$sql = $sql . <<<SQL
			AND
				`timestamp` > ?
SQL;
			return $this->findEntities($sql, array($convId, $user, $convId, $startpoint));
		} else {
			return $this->findEntities($sql, array($convId, $user, $convId));
		}

	}

	public function getMessagesByConvIdLimit($convId, $user, $limit){
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
						`joined`
					FROM
						*PREFIX*chat_och_users_in_conversation
					WHERE
						`user` = ?
					AND `conversation_id` = ?
				)
			ORDER BY timestamp DESC
			LIMIT ?,?
SQL;
		return $this->findEntities($sql, array($convId, $user, $convId, $limit[0], $limit[1]));
	}

}
