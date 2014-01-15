<?php

namespace OCA\Chat\Exceptions;

class CommandDataInvalid extends \Exception {

        public function __construct($msg){
                parent::__construct($msg);
        }

}