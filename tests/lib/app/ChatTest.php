<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

use \OCA\Chat\Db\Backend;
use OCA\Chat\OCH\Db\User;

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


	/**
	 * This test will run after the testGetContacts method
	 * This test will test if the cache is used instead of re-fetching the contacts information
	 * @depends testGetContacts
	 * @dataProvider contactsProvider
	 */
	public function testGetContactsCache($onlineUsers, $OCHBackend, $rawContacts, $parsedContacts){
		$this->app->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
			->disableOriginalConstructor()
			->getMock();

		$this->app->c['ContactsManager']->expects($this->never())
			->method('search');

		$result = $this->app->getContacts();
		$this->assertEquals($parsedContacts, $result);
	}

	/**
	 * @dataProvider backendProvider
	 */

	public function testGetBackends($backend){
		$this->app->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
			->disableOriginalConstructor()
			->getMock();

		// Mock the exist method so, that it returns true
		$this->app->c['BackendMapper']->expects($this->once())
			->method('getAllEnabled')
			->will($this->returnValue(array($backend)));

		$expectedResult = array();
		$expectedResult[$backend->getName()] = $backend;

		$result = $this->app->getBackends();

		$this->assertEquals($expectedResult, $result);

	}


	public function userContactProvider(){
		$OCHBackend = new Backend();
		$OCHBackend->setId(32);
		$OCHBackend->setDisplayname('ownCloud Handle');
		$OCHBackend->setName('och');
		$OCHBackend->setProtocol('x-owncloud-handle');
		$OCHBackend->setEnabled(true);
		return array(
			array(
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
					'id' => 'foo',
					'displayname' => 'foo',
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
				'foo'
			)
		);
	}

	/**
	 * @dataProvider userContactProvider
	 */
	public function testGetUserasContact($OCHBackend, $rawContacts, $expectedResult, $UID){
		$this->app->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->app->c['BackendMapper']->expects($this->any())
			->method('findByProtocol')
			->will($this->returnValue($OCHBackend));

		$this->app->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
			->disableOriginalConstructor()
			->getMock();

		$this->app->c['ContactsManager']->expects($this->any())
			->method('search')
			->will($this->returnValue($rawContacts));

		$result = $this->app->getUserasContact($UID);
		$this->assertEquals($expectedResult, $result);
	}

//	public function initConvsProvider(){
//		$conv1 = new User();
//		$conv1->setConversationId('CONV_ID_1408002874_42');
//		$conv1->setUser('admin');
//		$conv1->setJoined(329499626);
//		$conv1->setId(1);
//
//		$conv2 = new User();
//		$conv2->setConversationId('CONV_ID_1408002874_31');
//		$conv2->setUser('admin');
//		$conv2->setJoined(329443626);
//		$conv2->setId(2);
//
//		$conv3 = new User();
//		$conv3->setConversationId('CONV_ID_1408002874_26');
//		$conv3->setUser('admin');
//		$conv3->setJoined(324529443626);
//		$conv3->setId(3);
//
//		$currentUser = array(
//			'id' => 'admin',
//			'displayname' => 'admin',
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
//					'value' => 'admin',
//				),
//			),
//			'address_book_id' => 'local',
//			'address_book_backend' => '',
//		);
//
//		$joinRequestData1 = array(
//			"conv_id" => "CONV_ID_1408002874_42",
//			"user" => $currentUser
//		);
//
//		$joinRequestData2 = array(
//			"conv_id" => "CONV_ID_1408002874_31",
//			"user" => $currentUser
//		);
//
//		$joinRequestData3 = array(
//			"conv_id" => "CONV_ID_1408002874_26",
//			"user" => $currentUser
//		);
//
//
//		return array(
//			array(
//				array(
//					$conv1,
//					$conv2,
//					$conv3
//				),
//				array(
//					$joinRequestData1,
//					$joinRequestData2,
//					$joinRequestData3,
//				),
//				'admin'
//			)
//		);
//	}


}