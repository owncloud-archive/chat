<?php
namespace OCA\Chat\Responses;

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