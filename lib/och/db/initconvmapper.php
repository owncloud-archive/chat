<?php

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;
use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;

class InitConvMapper extends Mapper{

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_och_init_convs');
	}

	/**
	 * Deletes an entity from the table
	 *
	 * @param Entity $entity the entity that should be deleted
	 */
	public function deleteByConvAndUser(Entity $entity) {
		$sql = 'DELETE FROM `' . $this->tableName . '` WHERE `conv_id` = ? AND `user` = ?';
		$this->execute($sql, array(
				$entity->getConvId(),
				$entity->getUser()
		));
	}

	public function insertUnique(Entity $entity) {
		$sql = 'INSERT INTO `' . $this->tableName . '` (conv_id, user)
			SELECT * FROM (SELECT ?,?) AS tmp
			WHERE NOT EXISTS (
			SELECT conv_id, `user` FROM `' . $this->tableName .'` WHERE conv_id = ? AND `user` = ?
			) LIMIT 1';

		$this->execute($sql, array(
			$entity->getConvId(),
			$entity->getUser(),
			$entity->getConvId(),
			$entity->getUser(),
		));

	}
	
	public function findByUser($user){
		$sql = 'SELECT * FROM `' . $this->tableName . '` WHERE `user` = ?';
		return $this->findEntities($sql, array($user));
	}

}

