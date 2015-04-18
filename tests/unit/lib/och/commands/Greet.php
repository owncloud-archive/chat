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
use OCA\Chat\OCH\Db\UserOnline;

class GreetTest extends \PHPUnit_Framework_TestCase {

	public static $userOnline;

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
	
		// logic
		$greet = new Greet($this->container);
		$greet->setRequestData(array(
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
		));
		$result = $greet->execute();
	}

	public function testGeneratedSessionId(){
		$this->container['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('insert')
			->will($this->returnValue(true));

		$time = time();
		$greet = new Greet($this->container);
		$greet->setRequestData(array(
			'timestamp' => $time,
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
		));
		$result = $greet->execute();
		$expectedSessionId = md5("sessionID" . $time);

		$this->assertEquals($expectedSessionId, $result['session_id']);
	}

	public function testUserOnlineMapperInsert(){
		$this->container['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('insert')
			->will($this->returnCallback(function($userOnline){
				GreetTest::$userOnline = $userOnline;
			}));

		$time = time();
		$greet = new Greet($this->container);
		$greet->setRequestData(array(
			'timestamp' => $time,
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
		));
		$greet->execute();

		$expectedUserOnline = new UserOnline();
		$expectedUserOnline->setUser('admin');
		$expectedUserOnline->setSessionId(md5("sessionID" . $time));
		$expectedUserOnline->setLastOnline($time);

		$this->assertEquals($expectedUserOnline, GreetTest::$userOnline);

	}
}