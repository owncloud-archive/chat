<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');


use OCA\Chat\App\Chat;
use OCA\Chat\OCH\Db\PushMessage;
use OCA\Chat\OCH\Db\User;

// Refer to GreetTest::testDBFailure for a DBFailure test
// This almost the same for every unit test
class InviteTest extends \PHPUnit_Framework_TestCase {

	public static $pushMessage;

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
		$invite = new Invite($this->container);
		$invite->setRequestData(array(
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
			'user_to_invite' => array (
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
			//'conv_id' => 'addeimnpr',
		));
	}

	public function testOmmittedUserToInvite(){
		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'USER-TO-INVITE-MUST-BE-PROVIDED');

		// logic
		$invite= new Invite($this->container);
		$invite->setRequestData(array(
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
		));
	}


	public function testEmptyUserToInvite(){
		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'USER-TO-INVITE-MUST-BE-PROVIDED');

		// logic
		$invite= new Invite($this->container);
		$invite->setRequestData(array(
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
				'user_to_invite' => array()
		));
	}

	public function testUserEqualsUserToInvite(){
		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'USER-EQAUL-TO-USER-TO-INVITE');

		// logic
		$invite= new Invite($this->container);
		$invite->setRequestData(array(
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
				'user_to_invite' => array (
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
		));
	}

	public function testUserToInviteIsOCUser(){
		$this->container['API']->expects($this->once())
			->method('getUsers')
			->will($this->returnValue(array("admin", "herp"))); // Simulation of the OC users, derp is omitted
		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'USER-TO-INVITE-NOT-OC-USER');

		// logic
		$invite= new Invite($this->container);
		$invite->setRequestData(array(
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
			'user_to_invite' => array (
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
			'session_id' => 'c08809598b01894c468873fab54291aa',
			'timestamp' => 1397328934.658,
			'conv_id' => 'addeimnpr',
		));
	}

	public function testExecute(){
		$this->container['API']->expects($this->once())
			->method('getUsers')
			->will($this->returnValue(array("admin", "herp", "derp"))); // Simulation of the OC users

        $this->container['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();

        $this->container['PushMessageMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\PushMessageMapper')
			->disableOriginalConstructor()
			->getMock();

		$this->container['PushMessageMapper']->expects($this->any())
			->method('insert')
			->will($this->returnCallback(function($pushMessage){
				InviteTest::$pushMessage = $pushMessage;
			}));

        $this->container['UserMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserMapper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['UserMapper']->expects($this->any())
            ->method('insertUnique')
            ->will($this->returnValue(true));

        $this->container['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
            ->disableOriginalConstructor()
            ->getMock();

        $userToInviteSession = new User();
        $userToInviteSession->setUser('foo');
        $userToInviteSession->setSessionId(md5(time()));

        $this->container['UserOnlineMapper']->expects($this->any())
            ->method('findByUser')
            ->will($this->returnValue(array(
                    $userToInviteSession
                )
            ));

		$expectedPushMessage = new PushMessage();
		$expectedPushMessage->setSender('admin');
		$expectedPushMessage->setReceiver('foo');
		$expectedPushMessage->setReceiverSessionId(md5(time()));
		$expectedPushMessage->setCommand(json_encode(array(
			'type' => 'invite',
			'data' => array(
				'user' => 	array (
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
				'user_to_invite' => array (
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
			)
		)));

        // logic
		$invite= new Invite($this->container);
		$invite->setRequestData(array(
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
			'user_to_invite' => array (
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
			'session_id' => 'c08809598b01894c468873fab54291aa',
			'timestamp' => 1397328934.658,
			'conv_id' => 'addeimnpr',
		));
		$invite->execute();

		$this->assertEquals($expectedPushMessage, InviteTest::$pushMessage);
	}

}
