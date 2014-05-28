<?php
namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

class PushMessage extends Entity {

	public $sender;
	public $receiver;
	public $command;
	public $receiverSessionId;
	
	public function __construct(){

	}
}