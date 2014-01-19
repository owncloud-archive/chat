<?php

namespace OCA\Chat\Exceptions;

class RequestDataInvalid extends \Exception {

        public function __construct($msg){
                parent::__construct($msg);
        }

}