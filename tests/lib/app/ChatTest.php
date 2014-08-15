<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

use \OCA\Chat\Db\Backend;

class ChatTest extends \PHPUnit_Framework_TestCase {


	private $c;

	private $app;

	private static $returnValues;

	public function setUp(){
	}

	/**
	 *  Test that the backend is inserted into the DB when it doens't exists
	 */
	public function testRegisterBackendWhenItNotExists(){
		$chat = new Chat();

		$chat->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
			->disableOriginalConstructor()
			->getMock();

		// Mock the exist method so, that it returns true
		$chat->c['BackendMapper']->expects($this->any())
			->method('exists')
			->will($this->returnValue(false));

		// Mock the insert method so that it's set a class property as the entity it's called
		$chat->c['BackendMapper']->expects($this->any())
			->method('insert')
			->will($this->returnCallback(function($entity){
				ChatTest::$returnValues['testRegisterBackendWhenItNotExists']['backendMapper']['insert'] = $entity;
			}));


		// Create the expected entity which the `registerBackend` method should insert
		$expectedEntity = new Backend();
		$expectedEntity->setDisplayname('Foobar');
		$expectedEntity->setName('foo');
		$expectedEntity->setProtocol('x-foo');
		$expectedEntity->setEnabled(true);

		// Execute the function we are testing
		$chat->registerBackend('Foobar', 'foo', 'x-foo' , true);

		// Test that the $expectedEntity equals the entity created and inserted by the registerBackend method
		$this->assertEquals($expectedEntity, self::$returnValues['testRegisterBackendWhenItNotExists']['backendMapper']['insert']);

	}

	/**
	 *  Test that the backend isn't inserted into the DB when it already exists
	 */
	public function testRegisterBackendWhenItExists(){
		$chat = new Chat();

		$chat->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
			->disableOriginalConstructor()
			->getMock();

		// Mock the exist method so, that it returns true
		$chat->c['BackendMapper']->expects($this->any())
			->method('exists')
			->will($this->returnValue(true));

		// Mock the insert method so that it's set a class property as the entity it's called
		$chat->c['BackendMapper']->expects($this->any())
			->method('insert')
			->will($this->returnCallback(function($entity){
				ChatTest::$returnValues['testRegisterBackendWhenItNotExists']['backendMapper']['insert'] = true;
			}));

		// We set this to false
		// if the insert method on the BackendMapper is called this becomes true
		self::$returnValues['testRegisterBackendWhenItNotExists']['backendMapper']['insert'] = false;

		// Execute the function we are testing
		$chat->registerBackend('Foobar', 'foo', 'x-foo' , true);

		// Test that the $expectedEntity equals the entity created and inserted by the registerBackend method
		$this->assertEquals(false, self::$returnValues['testRegisterBackendWhenItNotExists']['backendMapper']['insert']);



	}

	public function testGetContacts(){
		$chat = new Chat();

		// Needed for the online//ofline state of the contacts
		$chat->c['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();
		$chat->c['UserOnlineMapper']->expects($this->any())
			->method('getOnlineUsers')
			->will($this->returnValue(array(
				'foo'
			)));

		// Needed to fetch the backend information
		$chat->c['BackendMapper'] = $this->getMockBuilder('\OCA\Chat\Db\BackendMapper')
			->disableOriginalConstructor()
			->getMock();
		$chat->c['BackendMapper']->expects($this->any())
			->method('findByProtocol')
			->will($this->returnCallback(function(){
				$backend = new Backend();
				$backend->setId(32);
				$backend->setDisplayname('ownCloud Handle');
				$backend->setName('och');
				$backend->setProtocol('x-owncloud-handle');
				$backend->setEnabled(true);
				return $backend;
			}));


		$chat->c['SyncOnlineCommand'] = $this->getMockBuilder('\OCA\Chat\OCH\Commands\SyncOnline')
			->disableOriginalConstructor()
			->getMock();

		// Contactsmanager is used to fetch the contacts
		$chat->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
			->disableOriginalConstructor()
			->getMock();

		// Return dummy contacts
		$chat->c['ContactsManager']->expects($this->any())
			->method('search')
			->will($this->returnValue(array (
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
			)));

		// This will be the reseult of the getContacts method
		// this data is used by the client
		$expectedResult = array(
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
			);

		$result = $chat->getContacts();
		$this->assertEquals($expectedResult, $result);

	}

}