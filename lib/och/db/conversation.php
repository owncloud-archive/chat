<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

/**
 * Class Conversation
 * @method null setConversationId(string $conversationId)
 * @method string getConversationId()
 */
class Conversation extends Entity {

	public $conversationId;
	
	public function __construct(){

	}
}