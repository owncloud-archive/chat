<?php
namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;
use \OCP\AppFramework\Db\Entity;

class UserMapper extends Mapper {
	
	private $userOnlineTable = '*PREFIX*chat_och_users_online';

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_och_users_in_conversation');
		$this->table = 'chat_och_users_in_conversation';
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
		$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
				'WHERE `user` = ? ';

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
		$sql = 'SELECT user FROM `' . $this->getTableName() . '` ' .
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
		$sql = 'INSERT INTO ' . $this->getTableName()
			 . ' SELECT * FROM (SELECT ?,?,?) AS tmp
				WHERE NOT EXISTS (
					SELECT  conversation_id, `user` FROM `' . $this->getTableName() .'` WHERE conversation_id = ? AND `user` = ?
				) LIMIT 1';

		$this->execute($sql, array(
				$entity->getConversationId(),
				$entity->getUser(),
				$entity->getJoined(),
				$entity->getConversationId(),
				$entity->getUser(),
		));

	}
	
}