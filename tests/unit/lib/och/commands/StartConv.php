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
	}

}
