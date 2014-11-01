<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\Db;

use \OCP\AppFramework\Db\Entity;

/**
 * Class Conversation
 * @method null setUser(string $user)
 * @method string getUser()
 * @method null setKey(string $key)
 * @method string getKey()
 * @method null setValue(int $value)
 * @method int getValue()
 * @method null setBackend(int $backend)
 * @method int getBackend()
 */
class Config extends Entity {

	public $user;
	public $key;
	public $value;
	public $backend;

}