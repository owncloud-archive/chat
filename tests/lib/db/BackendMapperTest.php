<?php

namespace OCA\Chat\Db;

use OCA\Chat\App\Chat;

class BackendMapperTest extends \PHPUnit_Framework_TestCase {

	public $app;

	public $backendMapper;

	public function setUp(){
		$this->app = new Chat();
		$this->backendMapper = $this->app->c['BackendMapper'];
	}

	public function testGetAll(){
		// Insert dummy data
		$backend = new Backend();
		$backend->setName('foo');
		$backend->setDisplayname('foo backend');
		$backend->setProtocol('x-foo');
		$backend->setEnabled(true);
		$this->backendMapper->insert($backend);

		$result = $this->backendMapper->getAll();
		$this->assertEquals($backends, $result);

	}

	public function tearDown(){

	}

}