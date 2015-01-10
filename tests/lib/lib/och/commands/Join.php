<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');


use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\Join;
use OCA\Chat\App\Chat;
use OCA\Chat\OCH\Db\InitConv;
use OCA\Chat\OCH\Db\PushMessage;
use \OCA\Chat\OCH\Db\UserOnline;
use OCA\Chat\Db\DBException;
use OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use OCA\Chat\OCH\Db\User;

// Refer to GreetTest::testDBFailure for a DBFailure test
// This almost the same for every unit test

// The GetUsers and Messages commands are tested in their testClassess
class JoinTest extends \PHPUnit_Framework_TestCase {

	public static $initConv;
	public static $pushMessages;
	public static $pushMessageNoGroup;

    public function setUp(){
		$app =  new Chat();
		$this->container = $app->getContainer();
		$this->container['API'] = $this->getMockBuilder('\OCA\Chat\Core\API')
			->disableOriginalConstructor()
			->getMock();
    	$this->container['API']->expects($this->any())
    		->method('log')
    		->will($this->returnValue(null));
    }
    
	public function testOmmittedConvId(){
		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'CONV-ID-MUST-BE-PROVIDED');
		
   		$this->container['API']->expects($this->any())
   			->method('prepareQuery')
   			->will($this->returnValue(true));
   	
	   	// logic
	   	$join = new Join($this->container);
	   	$join->setRequestData(array(
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
	   			//"conv_id" => "dasdfwffws" => this is ommitted
	   	));
   }

	public function testInitConvInsert(){
		$this->container['InitConvMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\InitConvMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['InitConvMapper']->expects($this->any())
			->method('insertUnique')
			->will($this->returnCallback(function($initConv){
				JoinTest::$initConv = $initConv;
			}));

		$this->container['GetUsersData'] = $this->getMockBuilder('\OCA\Chat\OCH\Data\GetUsers')
			->disableOriginalConstructor()
			->getMock();
		$this->container['GetUsersData']->expects($this->any())
			->method('execute')
			->will($this->returnValue(array("users" => array())));

		$this->container['MessagesData'] = $this->getMockBuilder('\OCA\Chat\OCH\Data\Messages')
			->disableOriginalConstructor()
			->getMock();
		$this->container['MessagesData']->expects($this->any())
			->method('execute')
			->will($this->returnValue(array("messages" => array())));

		$expectedInitConv =  new InitConv();
		$expectedInitConv->setConvId('dasdfwffws');
		$expectedInitConv->setUser('admin');

		$join = new Join($this->container);
		$join->setRequestData(array(
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
			'conv_id' => 'dasdfwffws'
		));
		$join->execute();

		$this->assertEquals($expectedInitConv, JoinTest::$initConv);
	}

	public function testPushMessageInsertGroupConv(){
		$this->container['InitConvMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\InitConvMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['InitConvMapper']->expects($this->any())
			->method('insertUnique')
			->will($this->returnValue(true));

		$this->container['GetUsersData'] = $this->getMockBuilder('\OCA\Chat\OCH\Data\GetUsers')
			->disableOriginalConstructor()
			->getMock();
		$this->container['GetUsersData']->expects($this->any())
			->method('execute')
			->will($this->returnValue(array("users" => array(
				0 => array (
					'id' => 'derp',
					'online' => false,
					'displayname' => 'derp',
					'backends' => array (
						'och' => array (
							'id' => NULL,
							'displayname' => 'ownCloud Handle',
							'protocol' => 'x-owncloud-handle',
							'namespace' => 'och',
							'value' => 'derp',
						),
					),
					'address_book_id' => 'admin',
					'address_book_backend' => 'localusers',
				),
				1 => array (
					'id' => 'admin',
					'online' => true,
					'displayname' => 'admin',
					'backends' => array (
						'och' => array (
							'id' => NULL,
							'displayname' => 'ownCloud Handle',
							'protocol' => 'x-owncloud-handle',
							'namespace' => 'och',
							'value' => 'admin',
						),
					),
					'address_book_id' => 'admin',
					'address_book_backend' => 'localusers',
				),
				2 => array (
					'id' => 'foo',
					'online' => true,
					'displayname' => 'foo',
					'backends' => array (
						'och' => array (
							'id' => NULL,
							'displayname' => 'ownCloud Handle',
							'protocol' => 'x-owncloud-handle',
							'namespace' => 'och',
							'value' => 'foo',
						),
					),
					'address_book_id' => 'admin',
					'address_book_backend' => 'localusers',

				),
			))));

		$this->container['MessagesData'] = $this->getMockBuilder('\OCA\Chat\OCH\Data\Messages')
			->disableOriginalConstructor()
			->getMock();
		$this->container['MessagesData']->expects($this->any())
			->method('execute')
			->will($this->returnValue(array("messages" => array())));

		$this->container['UserMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserMapper')
			->disableOriginalConstructor()
			->getMock();

		$derpSession = new User();
		$derpSession->setUser('derp');
		$derpSession->setSessionId('session-id-2');

		$adminSession = new User();
		$adminSession->setUser('admin');
		$adminSession->setSessionId('session-id-1');

		$this->container['UserMapper']->expects($this->any())
			->method('findSessionsByConversation')
			->will($this->returnValue(array($derpSession, $adminSession)));

		$this->container['PushMessageMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\PushMessageMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['PushMessageMapper']->expects($this->any())
			->method('insert')
			->will($this->returnCallback(function($pushMessage){
				JoinTest::$pushMessages[] = $pushMessage;
			}));

		$expectedInitConv =  new InitConv();
		$expectedInitConv->setConvId('dasdfwffws');
		$expectedInitConv->setUser('admin');

		$join = new Join($this->container);
		$join->setRequestData(array(
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
			'conv_id' => 'dasdfwffws'
		));
		$join->execute();

		$expectedPushMessage1 = new PushMessage();
		$expectedPushMessage1->setSender('admin');
		$expectedPushMessage1->setReceiver('derp');
		$expectedPushMessage1->setReceiverSessionId('session-id-2');
		$expectedPushMessage1->setCommand(json_encode(array(
			"type" => "joined",
			"data" => array(
				"conv_id" => 'dasdfwffws',
				"messages" => array(),
				"users" => array(
					0 => array (
						'id' => 'derp',
						'online' => false,
						'displayname' => 'derp',
						'backends' => array (
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'derp',
							),
						),
						'address_book_id' => 'admin',
						'address_book_backend' => 'localusers',
					),
					1 => array (
						'id' => 'admin',
						'online' => true,
						'displayname' => 'admin',
						'backends' => array (
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'admin',
							),
						),
						'address_book_id' => 'admin',
						'address_book_backend' => 'localusers',
					),
					2 => array (
						'id' => 'foo',
						'online' => true,
						'displayname' => 'foo',
						'backends' => array (
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'admin',
						'address_book_backend' => 'localusers',
					),
				)
			)
		)));

		$expectedPushMessage2 = new PushMessage();
		$expectedPushMessage2->setSender('admin');
		$expectedPushMessage2->setReceiver('admin');
		$expectedPushMessage2->setReceiverSessionId('session-id-1');
		$expectedPushMessage2->setCommand(json_encode(array(
			"type" => "joined",
			"data" => array(
				"conv_id" => 'dasdfwffws',
				"messages" => array(),
				"users" => array(
					0 => array (
						'id' => 'derp',
						'online' => false,
						'displayname' => 'derp',
						'backends' => array (
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'derp',
							),
						),
						'address_book_id' => 'admin',
						'address_book_backend' => 'localusers',
					),
					1 => array (
						'id' => 'admin',
						'online' => true,
						'displayname' => 'admin',
						'backends' => array (
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'admin',
							),
						),
						'address_book_id' => 'admin',
						'address_book_backend' => 'localusers',
					),
					2 => array (
						'id' => 'foo',
						'online' => true,
						'displayname' => 'foo',
						'backends' => array (
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'admin',
						'address_book_backend' => 'localusers',
					),
				)
			)
		)));

		$this->assertEquals(array($expectedPushMessage1,$expectedPushMessage2), JoinTest::$pushMessages);

	}

	public function testInsertNoPushMessagesNoGroupConv(){
		$this->container['InitConvMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\InitConvMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['InitConvMapper']->expects($this->any())
			->method('insertUnique')
			->will($this->returnValue(true));

		$this->container['GetUsersData'] = $this->getMockBuilder('\OCA\Chat\OCH\Data\GetUsers')
			->disableOriginalConstructor()
			->getMock();
		$this->container['GetUsersData']->expects($this->any())
			->method('execute')
			->will($this->returnValue(array("users" => array(
				0 => array (
					'id' => 'derp',
					'online' => false,
					'displayname' => 'derp',
					'backends' => array (
						'och' => array (
							'id' => NULL,
							'displayname' => 'ownCloud Handle',
							'protocol' => 'x-owncloud-handle',
							'namespace' => 'och',
							'value' => 'derp',
						),
					),
					'address_book_id' => 'admin',
					'address_book_backend' => 'localusers',
				),
				1 => array (
					'id' => 'admin',
					'online' => true,
					'displayname' => 'admin',
					'backends' => array (
						'och' => array (
							'id' => NULL,
							'displayname' => 'ownCloud Handle',
							'protocol' => 'x-owncloud-handle',
							'namespace' => 'och',
							'value' => 'admin',
						),
					),
					'address_book_id' => 'admin',
					'address_book_backend' => 'localusers',
				),
			))));

		$this->container['MessagesData'] = $this->getMockBuilder('\OCA\Chat\OCH\Data\Messages')
			->disableOriginalConstructor()
			->getMock();
		$this->container['MessagesData']->expects($this->any())
			->method('execute')
			->will($this->returnValue(array("messages" => array())));

		$this->container['UserMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserMapper')
			->disableOriginalConstructor()
			->getMock();

		$derpSession = new User();
		$derpSession->setUser('derp');
		$derpSession->setSessionId('session-id-2');

		$adminSession = new User();
		$adminSession->setUser('admin');
		$adminSession->setSessionId('session-id-1');

		$this->container['UserMapper']->expects($this->any())
			->method('findSessionsByConversation')
			->will($this->returnValue(array($derpSession, $adminSession)));

		$this->container['PushMessageMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\PushMessageMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['PushMessageMapper']->expects($this->any())
			->method('insert')
			->will($this->returnCallback(function($pushMessage){
				JoinTest::$pushMessageNoGroup[] = $pushMessage;
			}));

		$expectedInitConv =  new InitConv();
		$expectedInitConv->setConvId('dasdfwffws');
		$expectedInitConv->setUser('admin');

		$join = new Join($this->container);
		$join->setRequestData(array(
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
			'conv_id' => 'dasdfwffws'
		));
		$join->execute();

		$this->assertEquals(null, JoinTest::$pushMessageNoGroup);
	}
}
