<?php

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');

use OCA\Chat\App\Chat;
use OCA\Chat\OCH\Db\InitConv;

// Refer to GreetTest::testDBFailure for a DBFailure test
// This almost the same for every unit test
class DeleteInitConvTest extends \PHPUnit_Framework_TestCase {

	public static $initConv;

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

	public function testDeleteInitConv(){
		$this->container['InitConvMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\InitConvMapper')
			->disableOriginalConstructor()
			->getMock();
		$this->container['InitConvMapper']->expects($this->any())
			->method('deleteByConvAndUser')
			->will($this->returnCallback(function($initConv){
				DeleteInitConvTest::$initConv = $initConv;
			}));

		$requestData = array(
			'conv_id' => 'random-conv-id',
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
		);

		$expectedInitConv = new InitConv();
		$expectedInitConv->setConvId('random-conv-id');
		$expectedInitConv->setUser('admin');


		$deleteInitConv = new DeleteInitConv($this->container);
		$deleteInitConv->setRequestData($requestData);
		$deleteInitConv->execute();

		$this->assertEquals($expectedInitConv, DeleteInitConvTest::$initConv);

	}
}
