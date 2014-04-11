<?php

namespace OCA\Chat\OCH;
use \OCP\AppFramework\IAppContainer;

/*
 * API Base Class
 */
abstract class ChatAPI {

    public $app;
    protected $requestData;

    public function __construct(IAppContainer $app){
        $this->app = $app;
    }

    abstract function setRequestData(array $requestData);

    public function getRequestData(){
        return $this->requestData;
    }

    abstract public function execute();	
}
