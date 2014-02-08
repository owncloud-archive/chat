<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Entity;

class User extends Entity {

    // Note: a field id is set automatically by the parent class
    public $conversationId;
    public $user;
	public $sessionId;
	
    public function __construct(){
    }

}