<?php

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');


use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\StartConv;
use OCA\Chat\Tests\Lib\Mocks\APIMock;
use OCA\Chat\DependencyInjection\DIContainer;

class StartConvTest extends \PHPUnit_Framework_TestCase {
    
    
    public function setUp(){
	$this->container = new DIContainer('chat');
	$this->container['ConversationMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\ConversationMapper')
	    ->disableOriginalConstructor()
	    ->getMock();
	
    }
    
    public function testConversationExists(){
	// config
	$this->container['ConversationMapper']->expects($this->any())
             ->method('exists')
             ->will($this->returnValue(true));
	
	// logic
	$startConv = new StartConv($this->container);
	$startConv->setRequestData(array(
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
	$result = $startConv->execute();
	$this->assertEquals(array("conv_id" => 'addeimnpr'), $result);
    }
    
}
