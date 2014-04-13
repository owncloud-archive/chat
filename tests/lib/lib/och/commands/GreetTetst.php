<?php

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');


use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\Greet;
use OCA\Chat\App\Chat;
use OCA\Chat\Db\DBException;
use OCA\Chat\OCH\Exceptions\RequestDataInvalid;

// DONE
class GreetTest extends \PHPUnit_Framework_TestCase {


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
}