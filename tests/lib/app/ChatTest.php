<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

use \OCA\Chat\Db\Backend;

class ChatTest extends \PHPUnit_Framework_TestCase {

	public $app;

	public function setUp(){
		$this->app = new Chat();
	}

	public function  backendProvider(){
		$entity1 = new Backend();
		$entity1->setDisplayname('Foobar');
		$entity1->setName('foo');
		$entity1->setProtocol('x-foo');
		$entity1->setEnabled(true);

		$entity2 = new Backend();
		$entity2->setDisplayname('test');
		$entity2->setName('foo');
		$entity2->setProtocol('x-bar');
		$entity2->setEnabled(0);

		$entity3 = new Backend();
		$entity3->setDisplayname('test');
		$entity3->setName('foo');
		$entity3->setProtocol('x-bar');
		$entity3->setEnabled('true');

		return array(
			array($entity1),
			array($entity2),
			array($entity3)
		);
	}

	/**
	 * Test that the backend is inserted into the DB when it doens't exists
	 * @dataProvider backendProvider
	 */
	public function testRegisterBackendWhenItNotExists($backend){
		$this->app->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
			->disableOriginalConstructor()
			->getMock();

		$this->app->c['BackendMapper']->expects($this->once())
			->method('exists')
			->with($this->equalTo($backend->getName()))
			->will($this->returnValue(false));

		$this->app->c['BackendMapper']->expects($this->once())
			->method('insert')
			->with($this->equalTo($backend));

		$this->app->registerBackend(
			$backend->getDisplayname(),
			$backend->getName(),
			$backend->getProtocol() ,
			$backend->getEnabled()
		);
	}

	/**
	 * Test that the backend isn't inserted into the DB when it already exists
	 * @dataProvider backendProvider
	 */
	public function testRegisterBackendWhenItExists($backend){
		$this->app->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
			->disableOriginalConstructor()
			->getMock();

		$this->app->c['BackendMapper']->expects($this->once())
			->method('exists')
			->with($this->equalTo($backend->getName()))
			->will($this->returnValue(true));

		$this->app->c['BackendMapper']->expects($this->never())
			->method('insert');

		$this->app->registerBackend(
			$backend->getDisplayname(),
			$backend->getName(),
			$backend->getProtocol(),
			$backend->getEnabled()
		);
	}

	public function contactsProvider(){
		$OCHBackend = new Backend();
		$OCHBackend->setId(32);
		$OCHBackend->setDisplayname('ownCloud Handle');
		$OCHBackend->setName('och');
		$OCHBackend->setProtocol('x-owncloud-handle');
		$OCHBackend->setEnabled(true);

		return array(
			array(
				array(
					'foo'
				),
				$OCHBackend,
				array (
					0 => array (
						'id' => 'foo',
						'FN' => 'foo',
						'EMAIL' => array (
						),
						'IMPP' => array (
							0 => 'x-owncloud-handle:foo',
						),
						'addressbook-key' => 'local',
					),
					1 => array (
						'id' => 'bar',
						'FN' => 'bar',
						'EMAIL' => array (

						),
						'IMPP' => array (
							0 => 'x-owncloud-handle:bar',
						),
						'addressbook-key' => 'local',
					),
					2 => array (
						'id' => '1',
						'N' => array (
							0 => '',
							1 => 'TestContact',
							2 => '',
							3 => '',
							4 => '',
						),
						'UID' => '1a2a30d7-4907-4d5c-8e4a-3e51cf89e55a@localhost',
						'FN' => 'TestContact',
						'addressbook-key' => 'local:1',
					)
				),
				array(
				'contacts' => array (
					0 => array (
						'id' => 'foo',
						'online' => true,
						'displayname' => 'foo',
						'order' => 1,
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
					1 => array (
						'id' => 'bar',
						'online' => false,
						'displayname' => 'bar',
						'order' => 2,
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'bar',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
					2 => array (
						'id' => '1',
						'online' => false,
						'displayname' => 'TestContact',
						'order' => 3,
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '1',
					),
				),
				'contactsList' => array (
					0 => 'foo',
					1 => 'bar',
					2 => '1'
				),
				'contactsObj' => array (
					'foo' => array (
						'id' => 'foo',
						'online' => true,
						'displayname' => 'foo',
						'order' => 1,
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
					'bar' => array (
						'id' => 'bar',
						'online' => false,
						'displayname' => 'bar',
						'order' => 2,
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'bar',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
					1 => array (
						'id' => '1',
						'online' => false,
						'displayname' => 'TestContact',
						'order' => 3,
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '1',
					),
				)
			),
			)
		);
	}

	/**
	 * @dataProvider contactsProvider
	 */
	public function testGetContacts($onlineUsers, $OCHBackend, $rawContacts, $parsedContacts){
		$this->app->c['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->app->c['UserOnlineMapper']->expects($this->any())
			->method('getOnlineUsers')
			->will($this->returnValue($onlineUsers));

		$this->app->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->app->c['BackendMapper']->expects($this->any())
			->method('findByProtocol')
			->will($this->returnValue($OCHBackend));


		$this->app->c['SyncOnlineCommand'] = $this->getMockBuilder('\OCA\Chat\OCH\Commands\SyncOnline')
			->disableOriginalConstructor()
			->getMock();

		$this->app->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
			->disableOriginalConstructor()
			->getMock();

		$this->app->c['ContactsManager']->expects($this->any())
			->method('search')
			->will($this->returnValue($rawContacts));

		$result = $this->app->getContacts();
		$this->assertEquals($parsedContacts, $result);
	}


//	/**
//	 * This test will run after the testGetContacts method
//	 * This test will test if the cache is used instead of re-fetching the contacts information
//	 * @depends testGetContacts
//	 */
//	public function testGetContactsCache($expectedResult){
//		$chat = new Chat();
//		$chat->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
//			->disableOriginalConstructor()
//			->getMock();
//
//		 Return dummy contacts
//		$chat->c['ContactsManager']->expects($this->any())
//			->method('search')
//			->will($this->returnCallback(function(){
//				ChatTest::$returnValues['testGetContactsCache']['search_executed'] = true;
//			}));
//
//		// this will become true when the search function on the contactsmanager is executed
//		self::$returnValues['testGetContactsCache']['search_executed'] = false;
//
//		$result = $chat->getContacts();
//
//		$this->assertEquals(false, self::$returnValues['testGetContactsCache']['search_executed']);
//		$this->assertEquals($expectedResult, $result);
//	}
//
//
//	public function testGetBackends(){
//		$chat = new Chat();
//
//		$backend1 = new Backend();
//		$backend1->setDisplayname('Foobar');
//		$backend1->setName('foo');
//		$backend1->setProtocol('x-foo');
//		$backend1->setEnabled(true);
//
//		$backend2 = new Backend();
//		$backend2->setDisplayname('bar');
//		$backend2->setName('barfoo');
//		$backend2->setProtocol('x-bar');
//		$backend2->setEnabled(true);
//
//		$chat->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
//			->disableOriginalConstructor()
//			->getMock();
//
//		// Mock the exist method so, that it returns true
//		$chat->c['BackendMapper']->expects($this->any())
//			->method('getAllEnabled')
//			->will($this->returnValue(array($backend1, $backend2)));
//
//		$expectedResult = array();
//		$expectedResult['foo'] = $backend1;
//		$expectedResult['barfoo'] = $backend2;
//
//		$result = $chat->getBackends();
//
//		$this->assertEquals($expectedResult, $result);
//
//	}
//
//
//	public function testGetUserasContact(){
//		$chat = new Chat();
//
//		// Needed to fetch the backend information
//		$chat->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
//			->disableOriginalConstructor()
//			->getMock();
//		$chat->c['BackendMapper']->expects($this->any())
//			->method('findByProtocol')
//			->will($this->returnCallback(function(){
//				$backend = new Backend();
//				$backend->setId(32);
//				$backend->setDisplayname('ownCloud Handle');
//				$backend->setName('och');
//				$backend->setProtocol('x-owncloud-handle');
//				$backend->setEnabled(true);
//				return $backend;
//			}));
//
//		$chat->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
//			->disableOriginalConstructor()
//			->getMock();
//
//		// Return dummy contacts
//		$chat->c['ContactsManager']->expects($this->any())
//			->method('search')
//			->will($this->returnValue(array (
//				0 => array (
//					'id' => 'foo',
//					'FN' => 'foo',
//					'EMAIL' => array (
//					),
//					'IMPP' => array (
//						0 => 'x-owncloud-handle:foo',
//					),
//					'addressbook-key' => 'local',
//				),
//				1 => array (
//					'id' => 'bar',
//					'FN' => 'bar',
//					'EMAIL' => array (
//
//					),
//					'IMPP' => array (
//						0 => 'x-owncloud-handle:bar',
//					),
//					'addressbook-key' => 'local',
//				),
//				2 => array (
//					'id' => '1',
//					'N' => array (
//						0 => '',
//						1 => 'TestContact',
//						2 => '',
//						3 => '',
//						4 => '',
//					),
//					'UID' => '1a2a30d7-4907-4d5c-8e4a-3e51cf89e55a@localhost',
//					'FN' => 'TestContact',
//					'addressbook-key' => 'local:1',
//				)
//			)));
//
//		// This will be the result of the getContacts method
//		// this data is used by the client
//		$expectedResult = array(
//			'id' => 'foo',
//			'displayname' => 'foo',
//			'backends' => array (
//				'email' => array (
//					'id' => NULL,
//					'displayname' => 'E-mail',
//					'protocol' => 'email',
//					'namespace' => ' email',
//					'value' => array (
//						0 => array (
//						),
//					),
//				),
//				'och' => array (
//					'id' => NULL,
//					'displayname' => 'ownCloud Handle',
//					'protocol' => 'x-owncloud-handle',
//					'namespace' => 'och',
//					'value' => 'foo',
//				),
//			),
//			'address_book_id' => 'local',
//			'address_book_backend' => '',
//		);
//
//		$result = $chat->getUserasContact('foo');
//		$this->assertEquals($expectedResult, $result);
//
//		return $expectedResult;
//	}
//
//	public function	testGetInitConvs(){
//
//	}


}