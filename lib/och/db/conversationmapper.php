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

class ConversationMapper extends Mapper {

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_och_conversations'); // tablename is news_feeds
	}

	public function deleteConversation($conversationID){
		$sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE `conversation_id` = ? ';
		$this->execute($sql, array($conversationID));
	}

	public function findByConversationId($conversationID){
		$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' . 'WHERE `conversation_id` = ?';
		return $this->findEntity($sql, array($conversationID));
	}

	public function existsByConvId($id){
		$sql = 'SELECT `conversation_id` FROM `' . $this->getTableName() . '` ' . 'WHERE `conversation_id` = ?';
		$result = $this->execute($sql, array($id));
		if(count($result->fetchAll()) === 1){
			return true;
		} else {
			return false;
		}
	}

	public function existsByUsers($users){
		$usersCount = count($users);
		$sql = <<<SQL
			SELECT
				DISTINCT c1.conversation_id AS conv_id
			FROM
				*PREFIX*chat_och_users_in_conversation c1
			WHERE EXISTS (
				SELECT
					1
				FROM
					*PREFIX*chat_och_users_in_conversation c2
				WHERE
					c1.conversation_id = c2.conversation_id
				AND
				 	c2.user = ?
			)
SQL;

		for($i = 0; $i < ($usersCount -1); $i++){
			$sql .= <<<SQL
			AND EXISTS (
				SELECT
					1
				FROM
				 	*PREFIX*chat_och_users_in_conversation c2
				WHERE
					c1.conversation_id = c2.conversation_id
				AND
					c2.user = ?
			)
SQL;
		}

		$sql .= <<<SQL
			AND NOT EXISTS (
				SELECT
					1
				FROM
					*PREFIX*chat_och_users_in_conversation  c2
			 	WHERE
			 		c1.conversation_id = c2.conversation_id
			 	AND
			 		c2.user NOT IN (
SQL;

		foreach($users as $key=>$user){
			if($key === $usersCount-1){
				$sql .= " ?";
			} else {
				$sql .= " ?,";
			}
		}
		$sql .= <<<SQL
				)
			)
SQL;

		$params = array();

		foreach($users as $user){
			$params[] = $user;
		}

		foreach($users as $user){
			$params[] = $user;
		}

		try{
			$result = $this->execute($sql, $params);
			$row = $result->fetchRow();
			return $row;
		} catch (DoesNotExistException $exception) {
			var_dump($exception);
			return false;
		}
	}
}