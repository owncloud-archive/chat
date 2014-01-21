<?php

namespace OCA\Chat\Db;


/**
* This is returned or should be returned when a find request does not find an
* entry in the database
*/
class DoesNotExistException extends \Exception {

        /**
         * Constructor
         * @param string $msg the error message
         */
        public function __construct($msg){
                parent::__construct($msg);
        }

}