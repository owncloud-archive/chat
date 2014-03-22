<?php

namespace OCA\Chat\OCH\Exceptions;

class RequestDataInvalid extends \Exception {

    public function __construct($msg){
        parent::__construct($msg);
    }

}