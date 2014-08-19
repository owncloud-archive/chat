<?php

namespace OCA\Chat\Db;

use OCA\Chat\App\Chat;

/**
 * Class BackendMapperTest
 */
class BackendMapperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	public $app;

	/**
	 * @var \OCA\Chat\Db\BackendMapper
	 */
	public $backendMapper;

	public function setUp(){
		$this->app = new Chat();
		$this->backendMapper = $this->app->c['BackendMapper'];
	}

	public function backendsProvider(){
		$backends = array();
		$backend = new Backend();
		$backend->setName('foo');
		$backend->setDisplayname('foo backend');
		$backend->setProtocol('x-foo');
		$backend->setEnabled(true);
		$backends[] = $backend;
		return array(
			array(
				$backends
			)
		);
	}

	/**
	 * @dataProvider backendsProvider
	 */
	public function testGetAll($backends){
		// Insert dummy data
		foreach ($backends as $backend){
			$this->backendMapper->insert($backend);
		}

		$results = $this->backendMapper->getAll();
		foreach($results as $key=>$result){
			$this->assertEquals($backends[$key]->getName(), $result->getName());

		}
	}


	/**
	 * Remove all records from the table so future test can run without problems
	 */
	public function tearDown(){
		$query = \OCP\DB::prepare('DELETE FROM `' . $this->backendMapper->getTableName() . '`');
		$query->execute(array());
	}

}