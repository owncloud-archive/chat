<?php
namespace OCA\Chat\Responses;

use \OCA\AppFramework\Http\JSONResponse;

class Error extends JSONResponse {

	public function __construct($commandType, $errorMsg){
		parent::__construct(array(
			"type" => $commandType,
			"http_type"=> "response",
			"data" => array(
			"status" => "error",
				"msg" => $errorMsg
			)
		));
	}

}