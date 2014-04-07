<?php

namespace OCA\Chat\OCH\Commands;

include(__DIR__ . '/../../autoloader.php');

use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\StartConv;

class StartConvTest extends \PHPUnit_Framework_TestCase {
    
    public function testGenerateConvId(){
	$api = new API('chat');
	$expectedResult = 'adimn';
	$startConv = new StartConv($api);
	$result = $startConv->generateConvId(array('admin'));
	
	$this->assertEquals($expectedResult, $result);
    }
    
}
