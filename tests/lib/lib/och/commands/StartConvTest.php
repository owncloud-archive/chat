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

		$this->container['API'] = $this->getMockBuilder('\OCA\Chat\Core\API')
			->disableOriginalConstructor()
			->getMock();

		$this->container['InitConvMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\InitConvMapper')
			->disableOriginalConstructor()
			->getMock();
		
		$this->container['API']->expects($this->any())
			->method('log')
			->will($this->returnValue(null));
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

		$this->container['UserOnlineMapper']->expects($this->any())
			->method('getOnlineUsers')
			->will($this->returnValue(array("derp", "admin")));

		$session1 = new UserOnline();
		$session1->setUser('admin');
		$session1->setSessionId('session1id'); // must be deleted
		$session1->setLastOnline(time() - 200);

		$this->container['API']->expects($this->any())
			->method('getUsers')
			->will($this->returnValue(array("admin", "derp")));

		$this->container['UserOnlineMapper']->expects($this->any())
			->method('findByUser')
			->will($this->returnValue(array($session1))); // Simulation of the online users -> derp is offline

		$this->container['InitConvMapper']->expects($this->any())
			->method('insertUnique')
			->will($this->returnValue(true)); // Simulation of the online users -> derp is offline
		
		
		$this->container['PushMessageMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\PushMessageMapper')
			->disableOriginalConstructor()
			->getMock();

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
                    array(
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
            )
		));
		$result = $startConv->execute();
		$this->assertEquals(array("conv_id" => 'addeimnpr'), $result);
}

}
