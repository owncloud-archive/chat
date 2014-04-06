<?php

namespace OCA\Chat\OCH\Db;

use OCA\Chat\Db\Entity;

class Message extends Entity{

    public $id;
    
    public $convid;
    
    public $timestamp;
    
    public $user;
    
    public $message;
    
    
    public function __construct(){
	
    }
    
}
