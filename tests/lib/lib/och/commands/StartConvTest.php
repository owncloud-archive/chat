<?php

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');


use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\StartConv;
use OCA\Chat\App\Chat;
use \OCA\Chat\OCH\Db\UserOnline;

class StartConvTest extends \PHPUnit_Framework_TestCase {
    
    
    public function setUp(){
		$app =  new Chat();
		$this->container = $app->getContainer();
		$this->container['ConversationMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\ConversationMapper')
		    ->disableOriginalConstructor()
		    ->getMock();
		
		$this->container['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();
		
		$this->container['PushMessageMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\PushMessageMapper')
			->disableOriginalConstructor()
			->getMock();

		$this->container['UserMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserMapper')
			->disableOriginalConstructor()
			->getMock();
    }
    
    public function testConversationExistsDBError(){
    	
    }
    
    
    public function testConversationExists(){
		// config
		$this->container['ConversationMapper']->expects($this->any())
             ->method('exists')
             ->will($this->returnValue(true));
		
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('insert')
			->will($this->returnValue(true));
		
		$session1 = new UserOnline(); 
		$session1->setUser("derp"); // = username of user_to_invite
		$session1->setSessionId('87ce2b3faeb92f0fb745645e7827f51a');
		
		$session2 = new UserOnline();
		$session1->setUser("derp"); // = username of user_to_invite
		$session1->setSessionId('87ce2b3faeb92f0fasdf78as5d55fb745645e7827f51a');
		
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('findByUser')
			->will($this->returnValue(array($session1, $session2)));
		
		$this->container['PushMessageMapper']->expects($this->any())
			->method('insert')
			->will($this->returnValue(true));
		
		$this->container['UserMapper']->expects($this->any())
			->method('insert')
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
