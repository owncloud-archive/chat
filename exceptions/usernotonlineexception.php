<?php 
namespace OCA\Chat\Exceptions;

class UserNotOnlineException extends \Exception {

        /**
         * Constructor
         * @param string $msg the error message
         */
        public function __construct($msg){
                parent::__construct($msg);
        }

}