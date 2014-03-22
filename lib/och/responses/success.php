<?php
namespace OCA\Chat\OCH\Responses;

use \OCP\AppFramework\Http\JSONResponse;

class Success extends JSONResponse{

    public function __construct($requestType, $action, $data=array()){
        $data["status"] = "success";
        parent::__construct(array(
            "type" => $requestType . "::" . $action . "::response" ,
            "data" => $data
        ));
    }
}