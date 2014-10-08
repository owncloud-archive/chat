<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

/**
 * @method null setConvId( string $convId)
 * @method string getConvId()
 * @method null setTimestamp( int $timestamp)
 * @method int getTimestamp()
 * @method null setUser( string $user)
 * @method string getUser()
 * @method null setMessage( string $message)
 * @method string getMessage()
 */
class Message extends Entity{

	public $convid;

	public $timestamp;

	public $user;

	public $message;


	public function __construct(){
		$this->addType('timestamp', 'integer');

	}

}
