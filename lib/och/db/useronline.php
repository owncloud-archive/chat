<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

/**
 * @method null setUser( string $user)
 * @method string getUser()
 * @method null setSessionId(string $sessionId)
 * @method string getSessionId()
 * @method null setLastOnline(int $timestamp)
 * @method int getLastOnline()
 */
class UserOnline extends Entity {

	public $user;
	public $sessionId;
	public $lastOnline;
	
	public function __construct(){
        $this->addType('lastOnline', 'integer');

	}
}