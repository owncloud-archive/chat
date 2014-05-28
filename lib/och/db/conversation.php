<?php
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