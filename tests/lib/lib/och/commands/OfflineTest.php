<?php

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');


use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\Offline;
use OCA\Chat\App\Chat;

// DONE
class OfflineTest extends \PHPUnit_Framework_TestCase {


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
	
	public function testExecute(){
		
		$this->container['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();

		$this->container['UserMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserMapper')
			->disableOriginalConstructor()
			->getMock();
		
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('deleteBySessionId')
			->will($this->returnValue(true));
		
		$this->container['UserOnlineMapper']->expects($this->any())
			->method('getAll')
			->will($this->returnValue(array()));
		
		$this->container['UserMapper']->expects($this->any())
			->method('findBySessionId')
			->will($this->returnValue(array()));
		
		$offline = new Offline($this->container);
		$offline->execute();
	}

}