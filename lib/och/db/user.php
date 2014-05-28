<?php
namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

class User extends Entity {

	// Note: a field id is set automatically by the parent class
	public $conversationId;
	public $user;
	public $sessionId;
	public $joined;

	public function __construct(){

	}
}