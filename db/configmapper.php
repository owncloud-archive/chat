<?php

namespace OCA\Chat\Db;

use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;

class ConfigMapper extends Mapper {

	/**
	 * @var string ownCloud user id
	 */
	private $user;

	public function __construct(IDb $api, $user){
		parent::__construct($api, 'chat_config');
		$this->user = $user;
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
					`backend` = 'xmpp'
SQL;
		$values = array();
		$result = $this->findEntities($sql, array($this->user));
		foreach ($result as $r) {
			$values[$r->getKey()]  = $r->getValue();
		}
		return $values;
	}

	/**
	 * @param $backend id of the backend
	 * @param $key key of the config value
	 * @param $value the config value
	 */
	public function setConfigValue($backend, $key, $value){

	}


}