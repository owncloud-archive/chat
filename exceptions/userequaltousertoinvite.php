<?php 
namespace OCA\Chat\Exceptions;

class UserEqualToUserToInvite extends \Exception {

        /**
         * Constructor
         * @param string $msg the error message
         */
        public function __construct($msg){
                parent::__construct($msg);
        }

}