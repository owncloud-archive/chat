<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\Db;


/**
* This is returned or should be returned when a find request does not find an
* entry in the database
*/
class DoesNotExistException extends \Exception {

}