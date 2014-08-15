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

}