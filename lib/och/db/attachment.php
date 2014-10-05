<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use \OCP\AppFramework\Db\Entity;

/**
 * Class Conversation
 * @method null setOwner(string $owner)
 * @method string getOwner()
 * @method null setPath(string $path)
 * @method string getPath()
 * @method null setFileId(int $fileId)
 * @method int getFileId()
 * @method null setTimestamp(int $timestamp)
 * @method int getTimestamp()
 * @method null setConvId(string $convId)
 * @method string getConvId()
 */
class Attachment extends Entity {

	public $owner;
	public $path;
	public $fileId;
	public $timestamp;
	public $convId;

	public function __construct(){

	}
}