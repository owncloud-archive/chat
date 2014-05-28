<?php

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

/**
 * @method null setConvId(string $convId)
 * @method string getConvId()
 * @method null setUser(string $user)
 * @method string getUser()
 */
class InitConv extends Entity{

	public $convId;

	public $user;

	public function __construct(){

	}

}
