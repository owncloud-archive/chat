<?php
namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;

class UserOnlineMapper extends Mapper {

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_och_users_online'); // tablename is news_feeds
	}

	public function getOnlineUsers(){
		$sql = <<<SQL
			SELECT
				`user`
			FROM
				`*PREFIX*chat_och_users_online`
SQL;
		$result = $this->execute($sql);

		$rows = array();

		while($row = $result->fetch()){
			array_push($rows, $row['user']);
		}
		return $rows;
	}

	public function getAll(){
		return $this->findEntities("SELECT * FROM " . $this->getTableName());
	}
	
	public function findByUser($user){
		$sql = <<<SQL
			SELECT
				*
			FROM `*PREFIX*chat_och_users_online`
			WHERE
				`user` = ?
SQL;
		$result = $this->findEntities($sql, array($user));
		return $result;
	}

	public function deleteBySessionId($sessionID){
		$sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE `session_id` = ?';
		$this->execute($sql, array($sessionID));
	}
	
	public function updateLastOnline($sessionID, $timestamp){
		$sql = 'UPDATE `' . $this->getTableName() . '` SET `last_online` = ? WHERE `session_id` = ?';
		$this->execute($sql, array($timestamp, $sessionID));
	}
 
}
