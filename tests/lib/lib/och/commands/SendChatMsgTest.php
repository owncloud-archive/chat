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
use OCA\Chat\OCH\Commands\SendChatMsg;
use OCA\Chat\App\Chat;
use OCA\Chat\Db\DBException;
use OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use OCA\Chat\OCH\Db\UserOnline;
use OCA\Chat\OCH\Db\User;
use OCA\Chat\OCH\Db\PushMessage;

// DONE
class SendChatMsgTest extends \PHPUnit_Framework_TestCase {

	public static $command;
	public static $pushMessageCount;
	
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
		$sendChatMsg = new SendChatMsg($this->container);
		$sendChatMsg->setRequestData(array(
			'user' => array (
				'id' => 'admin',
				'online' => false,
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
			'session_id' => 'c08809598b01894c468873fab54291aa',
			'timestamp' => 1397328934.658,
			'chat_msg' => "TestMsg",
			//'conv_id' => 'addeimnpr',	
		));
	}
	
	public function testOmmittedChatMsg(){
		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'CHAT-MSG-MUST-BE-PROVIDED');
	
		$this->container['API']->expects($this->any())
			->method('prepareQuery')
			->will($this->returnValue(true));
	
		// logic
		$sendChatMsg = new SendChatMsg($this->container);
		$sendChatMsg->setRequestData(array(
			'user' => array (
				'id' => 'admin',
				'online' => false,
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
			'session_id' => 'c08809598b01894c468873fab54291aa',
			'timestamp' => 1397328934.658,
			'conv_id' => 'addeimnpr',
			//'chat_msg' => ''
		));
	}
	
	public function testOmmittedTimestamp(){
		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'TIMESTAMP-MUST-BE-PROVIDED');
	
		$this->container['API']->expects($this->any())
			->method('prepareQuery')
			->will($this->returnValue(true));
	
		// logic
		$sendChatMsg = new SendChatMsg($this->container);
		$sendChatMsg->setRequestData(array(
			'user' => array (
				'id' => 'admin',
				'online' => false,
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
			'session_id' => 'c08809598b01894c468873fab54291aa',
			//'timestamp' => 1397328934.658,
			'conv_id' => 'addeimnpr',
			'chat_msg' => 'test'
		));
		
	}
	
	// Test if the created command in sendchatmsg.php is okay and is only created for recevier !== sender
	public function testCreatedCommand(){
		
		$this->container['PushMessageMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\PushMessageMapper')
			->disableOriginalConstructor()
			->getMock();
		
		$this->container['PushMessageMapper']->expects($this->any())
			->method('insert')
			->will($this->returnCallback(function($pushMessage){
				SendChatMsgTest::$command = $pushMessage->getCommand();
				SendChatMsgTest::$pushMessageCount++;
			}));
			
			
		$user1 = new User(); // This is a receiver
		$user1->setConversationId('addeimnpr');
		$user1->setUser('derp');
		$user1->setSessionId('c08809598b01894c4asdfasdf68873fab54291aa');
		
		$user2 = new User(); // This is a sender but is also stored in the DB -> no need to create a pushmessage
		$user2->setConversationId('addeimnpr');
		$user2->setUser('admin');
		$user2->setSessionId('c08809598b01894c468873fab54291aa');
		
		$this->container['UserMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserMapper')
			->disableOriginalConstructor()
			->getMock();
		
		$this->container['UserMapper']->expects($this->any())
			->method('findSessionsByConversation')
			->will($this->returnValue(array($user1, $user2)));

	
		$this->container['MessageMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\MessageMapper')
			->disableOriginalConstructor()
			->getMock();
		
		$sendChatMsg = new SendChatMsg($this->container);
		$sendChatMsg->setRequestData(array(
			'user' => array (
				'id' => 'admin',
				'online' => false,
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
			'session_id' => 'c08809598b01894c468873fab54291aa',
			'timestamp' => 1397328934.658,
			'conv_id' => 'addeimnpr',
			'chat_msg' => 'test'
		));
		$sendChatMsg->execute();

		$expectedCommand = json_encode(array(
            'type' => 'send_chat_msg',
            'data' => array(
                'user' => array (
					'id' => 'admin',
					'online' => false,
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
                'conv_id' => 'addeimnpr',
                'timestamp' => 1397328934.658, 
                'chat_msg' => 'test'
            )
        ));	
		
		$this->assertEquals($expectedCommand, SendChatMsgTest::$command);
		$this->assertEquals(1, SendChatMsgTest::$pushMessageCount);
		
		
	}
	
}