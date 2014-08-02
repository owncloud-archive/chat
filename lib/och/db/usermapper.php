<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use Doctrine\DBAL\Sharding\SQLAzure\SQLAzureFederationsSynchronizer;
use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;
use \OCP\AppFramework\Db\Entity;
use \OCP\AppFramework\Db\DoesNotExistException;

class UserMapper extends Mapper {
	
	private $userOnlineTable = '*PREFIX*chat_och_users_online';

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_och_users_in_conversation');
		$this->table = '*PREFIX*chat_och_users_in_conversation';
	}

	public function findSessionsByConversation($conversationId){
		$sql = <<<SQL
			SELECT
				$this->table.user,
				$this->userOnlineTable.session_id
			FROM
				$this->table
			INNER JOIN
				$this->userOnlineTable
			ON
				$this->table.user = $this->userOnlineTable.user
			AND
				$this->table.conversation_id = ?
SQL;
		$result = $this->findEntities($sql, array($conversationId));
		return $result;
	}

	public function findByUser($user){
		$sql = <<<SQL
			SELECT
				*
			FROM
				$this->table
			WHERE
				`user` = ?
SQL;
		$result = $this->findEntities($sql, array($user));
		return $result;
	}
	
	 public function findConvsIdByUser($user){
		$sql = 'SELECT conversation_id FROM `' . $this->getTableName() . '` ' .
				'WHERE `user` = ? ';

		$result = $this->execute($sql, array($user));

		$ids = array();
		while($row = $result->fetchRow()){
			array_push($ids, $row['conversation_id']);
		}

		$ids = array_unique($ids);
		return $ids;
	}
		

	public function findUsersInConv($id){
		$sql = 'SELECT `user` FROM `' . $this->getTableName() . '` ' .
				'WHERE `conversation_id` = ? ';

		$result = $this->execute($sql, array($id));

		$users = array();
		while($row = $result->fetch()){
			array_push($users, $row['user']);
		}

		$users = array_unique($users);
		return $users;
	}

	public function insertUnique(Entity $entity){
		// First check $entity is already in DB
		$sql = <<<SQL
			SELECT
				`user`
			FROM
				`*PREFIX*chat_och_users_in_conversation`
			WHERE
				`user` = ?
			AND
				`conversation_id` = ?
SQL;
		try {
			$result = $this->findOneQuery($sql, array($entity->getUser(), $entity->getConversationId()));
			// The user already joined the conv -> nothing to do
		} catch (\Exception $exception) {
			// The user isn't in this conversation -> add it
			$sql = <<<SQL
			INSERT
			INTO `*PREFIX*chat_och_users_in_conversation`
			(
				`user`,
				`conversation_id`,
				`joined`
			) VALUES (
				?,
				?,
				?
			)
SQL;
			$this->execute($sql, array(
				$entity->getUser(),
				$entity->getConversationId(),
				$entity->getJoined()
			));
		}
	}

	public function setArchived($convid, $archived, $user){
		$sql = <<<SQL
			UPDATE
				`*PREFIX*chat_och_users_in_conversation`
			SET
				`archived` = ?
			WHERE
				`conversation_id` = ?
			AND
				`user` = ?
SQL;
		$result = $this->execute($sql, array($archived, $convid, $user));
	}
	
}