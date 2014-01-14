<?php
namespace OCA\Chat\Commands;

use \OCA\AppFramework\Http\JSONResponse;

class SuccessCommandResponse extends JSONResponse{

	public function __construct($commandType){
		parent::__construct(array(
			"type" => $commandType,
			"http_type"=> "response",
			"data" => array(
				"status" => "success"
			)
		));
	}

}