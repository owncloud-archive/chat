<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Entity;

class User extends Entity {

    // Note: a field id is set automatically by the parent class
    public $conversationId;
    public $user;

    public function __construct(){
    }

}