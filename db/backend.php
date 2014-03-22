<?php
namespace OCA\Chat\Db;

use \OCA\Chat\Db\Entity;

class Backend extends Entity {

    // Note: a field id is set automatically by the parent class
    public $displayname;
    public $name;
    public $enabled;
    public $checked;
    public $protocol;

    public function __construct(){
        
    }
}
