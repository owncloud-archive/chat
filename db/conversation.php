<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Entity;

class Conversation extends Entity {

    // Note: a field id is set automatically by the parent class
    public $conversationId;

    public function __construct(){
    }

}