<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

/**
 * @method null setConversationId( string $convId)
 * @method string getConversationId()
 * @method null setUser( string $user)
 * @method string getUser()
 * @method string getSessionId()
 * @method null setJoined(int $timestamp)
 * @method int getJoined()
 */
class User extends Entity {

	public $conversationId;
	public $user;
	public $sessionId;
	public $joined;

	public function __construct(){

	}
}