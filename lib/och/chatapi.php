<?php

namespace OCA\Chat\OCH;
use \OCA\Chat\Core\API;

/*
 * API Base Class
 */
abstract class ChatAPI {

    public $api;
    protected $requestData;

    public function __construct(API $api){
        $this->api = $api;
    }

    abstract function setRequestData(array $requestData);

    public function getRequestData(){
        return $this->requestData;
    }

    abstract public function execute();	
}
