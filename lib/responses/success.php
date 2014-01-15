<?php
namespace OCA\Chat\Responses;

use \OCA\AppFramework\Http\JSONResponse;

class Success extends JSONResponse{

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