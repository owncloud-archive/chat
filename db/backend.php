<?php
/**
 * Copyright (c) 2014, Tobia De Koninck <hey@ledfan.be>
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\Db;

use \OCP\AppFramework\Db\Entity;

class Backend extends Entity {

	// Note: a field id is set automatically by the parent class
	public $displayname;
	public $name;
	public $enabled;
	public $checked;
	public $protocol;

	public function __construct(){

	}
}
