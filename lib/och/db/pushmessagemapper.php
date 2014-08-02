<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;
use \OCA\Chat\Db\DoesNotExistException;

class PushMessageMapper extends Mapper {

	public function __construct(IDb $api) {
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