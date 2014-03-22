<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Entity;

class PushMessage extends Entity {

    public $id;
    public $sender;
    public $receiver;
    public $command;
    public $receiverSessionId;
	
    public function __construct(){
    
    }
}