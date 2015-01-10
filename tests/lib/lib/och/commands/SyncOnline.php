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
use OCA\Chat\OCH\Commands\SyncOnline;
use OCA\Chat\App\Chat;
use OCA\Chat\Db\DBException;
use OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use OCA\Chat\OCH\Db\UserOnline;

// DONE
class SyncOnlineTest extends \PHPUnit_Framework_TestCase {

	public static $sessionIds;

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
		$synConline = new SyncOnline($this->container);
		$synConline->setRequestData(array());
		$result = $synConline->execute();
	}
	
	public function testExecute(){
		$session1 = new UserOnline();
		$session1->setUser('admin');
		$session1->setSessionId('session1id'); // must be deleted
		$session1->setLastOnline(time() - 200);
		
		$session2 = new UserOnline();
		$session2->setUser('derp');
		$session2->setSessionId('session2id'); // must be deleted
		$session2->setLastOnline(time() - 320);

		$session3 = new UserOnline(); 
		$session3->setUser('derp');
		$session3->setSessionId('session3id'); // must NOT be deleted
		$session3->setLastOnline(time() - 10);
		
		$this->container['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();
		
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('getAll')
			->will($this->returnValue(array($session1, $session2, $session3)));
		
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('deleteBySessionId')
			->will($this->returnCallback(function($sessionId){
				SyncOnlineTest::$sessionIds[] = $sessionId;
		}));
			
		$synConline = new SyncOnline($this->container);
		$synConline->setRequestData(array());
		$result = $synConline->execute();
		
		$this->assertEquals(array('session1id', 'session2id'), SyncOnlineTest::$sessionIds);		

	}
}