<?php

namespace OCA\Chat\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;

class ConfigMapper extends Mapper {

	/**
	 * @var string ownCloud user id
	 */
	private $user;

	/**
	 * @var \OCP\Security\ICrypto
	 */
	private $crypto;

	public function __construct(IDb $api, $user, $crypto){
		parent::__construct($api, 'chat_config');
		$this->user = $user;
		$this->crypto = $crypto;
	}

	/**
	 * @param $backend id of the backend
	 * @return array config Values
	 */
	public function getByBackend($backend){

		$sql = <<<SQL
				SELECT
					*
				FROM
					`*PREFIX*chat_config`
				WHERE
					`user` = ?
				AND
					`backend` = ?
SQL;
		$values = array();
		$result = $this->findEntities($sql, array($this->user, $backend));
		foreach ($result as $r) {
			$values[$r->getKey()]  = $this->crypto->decrypt($r->getValue());
		}
		return $values;
	}

	/**
	 * @param $backend id of the backend
	 * @param $key key of the config value
	 * @param $value the config value
	 */
	public function set($backend, $key, $value){
		$value = $this->crypto->encrypt($value);
		if($this->hasKey($backend, $key, $value)){
			$sql = <<<SQL
				UPDATE
					`*PREFIX*chat_config`
				SET
					`value` = ?
				WHERE
					`user` = ?
				AND
					`backend` = ?
				AND
					`key` = ?
SQL;
			$this->execute($sql, array($value, $this->user, $backend, $key));
		} else {
			$sql = <<<SQL
				INSERT
				INTO
					`*PREFIX*chat_config`
				(
					`user`,
					`key`,
					`value`,
					`backend`
				) VALUES (
					?,
					?,
					?,
					?
				)
SQL;
			$this->execute($sql, array($this->user, $key, $value, $backend));
		}
	}

	public function hasKey($backend, $key, $value){
		try {
			$sql = <<<SQL
			SELECT
				*
			FROM
				`*PREFIX*chat_config`
			WHERE
				`backend` = ?
			AND
				`key` = ?
			AND
				`user` = ?
SQL;
			$this->findEntity($sql, array($backend, $key, $this->user));
			return true;
		} catch (DoesNotExistException $e){
			return false;
		}
	}
	
}