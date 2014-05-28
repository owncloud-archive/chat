<?php
namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

/**
 * @method null setSender( string $sender)
 * @method string getSender()
 * @nethod null setReceiver( string $receiver)
 * @method string getReceiver()
 * @method null setCommand( string $command)
 * @method string getCommand()
 * @method null setReceiverSessionId( string $sessionId)
 * @method string getReceiverSessionId()
 */
class PushMessage extends Entity {

	public $sender;
	public $receiver;
	public $command;
	public $receiverSessionId;
	
	public function __construct(){

	}
}