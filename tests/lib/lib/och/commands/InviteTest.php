<?php

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');


use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\Invite;
use OCA\Chat\App\Chat;
use \OCA\Chat\OCH\Db\UserOnline;
use OCA\Chat\Db\DBException;
use OCA\Chat\OCH\Exceptions\RequestDataInvalid;

// DONE
class InviteTest extends \PHPUnit_Framework_TestCase {


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


	/**
	 * Testcase: if there is a PDOException in the datamapper a DBException must be thrown
	 * with the same message as in the PDOException
	 */
	public function testDBFailure(){
		$this->setExpectedException('\OCA\Chat\Db\DBException', 'Something went wrong with the DB!');
		// config
		$this->container['API']->expects($this->any())
			->method('prepareQuery')
			->will($this->throwException(new \PDOException('Something went wrong with the DB!')));

		$this->container['API']->expects($this->any())
			->method('getUsers')
			->will($this->returnValue(array("admin", "derp")));

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
			'conv_id' => 'addeimnpr',
		));
		$result = $invite->execute();
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



	public function testUserToInviteNotOnline(){
		$this->container['API']->expects($this->once())
			->method('getUsers')
			->will($this->returnValue(array("admin", "herp", "derp"))); // Simulation of the OC users
		$this->container['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('getOnlineUsers')
			->will($this->returnValue(array("admin"))); // Simulation of the online users -> derp is offline

		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'USER-TO-INVITE-NOT-ONLINE');

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
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('getOnlineUsers')
			->will($this->returnValue(array("admin", "derp"))); // Simulation of the online users -> derp is offline

		$session1 = new UserOnline();
		$session1->setUser('admin');
		$session1->setSessionId('session1id'); // must be deleted
		$session1->setLastOnline(time() - 200);

		$this->container['UserOnlineMapper']->expects($this->any())
			->method('findByUser')
			->will($this->returnValue(array($session1))); // Simulation of the online users -> derp is offline

		$this->container['PushMessageMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\PushMessageMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['PushMessageMapper']->expects($this->any())
			->method('insert')
			->will($this->returnValue(true)); // Simulation of the online users -> derp is offline


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
	}

}
