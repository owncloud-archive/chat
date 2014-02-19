<?php
namespace OCA\Chat\Db;

use \OCA\Chat\Db\Mapper;
use \OCA\Chat\Core\Api;


class BackendMapper extends Mapper {

	public function __construct(API $api) {
		parent::__construct($api, 'chat_backends'); // tablename is news_feeds
		$this->tableName = '*PREFIX*' . 'chat_backends';
	}
	
	public function getAll(){
		return $this->findEntities("SELECT * FROM " . $this->getTableName());
	}

	public function getAllEnabled(){
		return $this->findEntities("SELECT * FROM " . $this->getTableName() . " WHERE enabled='true'");
	}
	
	public function exists($name){
		$entities =  $this->findEntities("SELECT * FROM " . $this->getTableName() . " WHERE name=?", array($name));
		if (count($entities) === 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function findByProtocol($protocol){
		return  $this->findEntity("SELECT * FROM " . $this->getTableName() . " WHERE protocol=?", array($protocol));
	}
}