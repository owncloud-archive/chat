<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Responses;

use \OCP\AppFramework\Http\JSONResponse;

class Error extends JSONResponse {

	public function __construct($requestType, $action, $errorMsg){
		parent::__construct(array(
			"type" => $requestType . "::" . $action . "::response",
			"data" => array(
				"status" => "error",
				"msg" => $errorMsg
			)
		));
	}
}