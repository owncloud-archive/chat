<?php
namespace OCA\Chat\OCH\Responses;

use \OCP\AppFramework\Http\JSONResponse;

class Success extends JSONResponse{

	public function __construct($requestType, $action){
		parent::__construct(array(
			"type" => $requestType . "::" . $action . "::response" ,
			"data" => array(
				"status" => "success"
			)
		));
	}

}