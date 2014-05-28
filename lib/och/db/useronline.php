<?php
namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

class UserOnline extends Entity {

	// Note: a field id is set automatically by the parent class
	public $user;
	public $sessionId;
	public $lastOnline;
	
	public function __construct(){

	}
}