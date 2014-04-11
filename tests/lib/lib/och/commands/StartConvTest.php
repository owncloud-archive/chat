<?php

namespace OCA\Chat\OCH\Commands;

include(__DIR__ . '/../../autoloader.php');

use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\StartConv;
use OCA\Chat\Tests\Lib\Mocks\APIMock;

class StartConvTest extends \PHPUnit_Framework_TestCase {
    
    public function testGenerateConvId(){
	// config
	$api = new APIMock('chat');
	$api->prepareQueryMustReturn($value);
	$expectedResult = 'adimn';
	
	// logic
	$startConv = new StartConv($api);
	$result = $startConv->setRequestData(array(
	    "user" => array(
		"id" => "admin",
		"displayname"=> "admin",
		"backends" => array(
		    "och" => array(
			"id" => NULL,
			"displayname" => "wnCloud Handle",
			"protocol" => "x-owncloud-handle",
			"namespace" => "och",
			"value" => "admin"
		    )
		),
		"address_book_id" => "admin",
		"address_book_backend"=> "localusers",
	    ),
	    "session_id" => "87ce2b3faeb92f0fb745645e7827f51a",
	    "timestamp" => 1397193430.516,
	    "user_to_invite" => array(
		"id" => "derp",
		"displayname" => "derp",
		"backends" => array(
		    "och" => array(
			"id" => NULL,
			"displayname" => "wnCloud Handle",
			"protocol" => "x-owncloud-handle",
			"namespace" => "och",
			"value" => "derp"
		    )
		),
		"address_book_id" => "admin",
		"address_book_backend" => "localusers"
	    )
	));
	
	$this->assertEquals($expectedResult, $result);
    }
    
}
