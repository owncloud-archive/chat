<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Entity;

class PushMessage extends Entity {

    // Note: a field id is set automatically by the parent class
    public $id;
    public $sender;
    public $receiver;
    public $command;
	public $receiverSessionId;
	
    public function __construct(){
    }

}