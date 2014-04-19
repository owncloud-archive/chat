<?php

namespace OCA\Chat\OCH\Db;

use OCA\Chat\Db\Mapper;

class InitConvMapper extends Mapper{

	public function __construct(API $api) {
		parent::__construct($api, 'chat_och_init_convs');
	}

	/**
	 * Deletes an entity from the table
	 * @param Entity $entity the entity that should be deleted
	 */
	public function deleteByConvAndUser(Entity $entity){
		$sql = 'DELETE FROM `' . $this->tableName . '` WHERE `conv_id` = ? AND `user` = ?';
		$this->execute($sql, array($entity->getConvId(), $entity->getUser()));
	}

}
