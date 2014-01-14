<?php
namespace OCA\Chat\Commands;

use \OCA\AppFramework\Http\JSONResponse;

class ErrorCommandResponse extends JSONResponse {

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