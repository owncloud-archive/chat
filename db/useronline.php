<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Entity;

class UserOnline extends Entity {

    // Note: a field id is set automatically by the parent class
    public $user;
	public $sessionId;
	
    public function __construct(){
    }

}