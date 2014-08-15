<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\Db;

use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;

class BackendMapper extends Mapper {

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_backends'); // tablename is news_feeds
		$this->tableName = '*PREFIX*' . 'chat_backends';
	}
	
	public function getAll(){
		try {
			return  $this->findEntities("SELECT * FROM " . $this->getTableName());
		} catch (DoesNotExistException $e){

		}
	}

	public function getAllEnabled(){
		try {
			return $this->findEntities("SELECT * FROM " . $this->getTableName() . " WHERE enabled=?", array(true));
		} catch (DoesNotExistException $e){

		}
	}

	public function exists($name){
		$entities =  $this->findEntities("SELECT * FROM " . $this->getTableName() . " WHERE name=?", array($name));
		if (count($entities) === 0){
			return false;
		} else {
			return true;
		}
	}

	public function findByProtocol($protocol){
		return  $this->findEntity("SELECT * FROM " . $this->getTableName() . " WHERE protocol=?", array($protocol));
	}
}