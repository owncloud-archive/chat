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
		$backend1 = new Backend();
		$backend1->setName('foo');
		$backend1->setDisplayname('foo backend');
		$backend1->setProtocol('x-foo');
		$backend1->setEnabled(true);
		$backend2 = new Backend();
		$backend2->setName('bar');
		$backend2->setDisplayname('bar backend');
		$backend2->setProtocol('x-bar');
		$backend2->setEnabled(false);
		return array(
			array(
				array(
					1 => $backend1, // use the same id as in the DB
					2 => $backend2
				)
			)
		);
	}

	/**
	 * @dataProvider backendsProvider
	 */
	public function testGetAll(array $backends){
		$expectedBackends = array();
		// Insert dummy data
		foreach ($backends as $backend){
			$this->backendMapper->insert($backend);
			$expectedBackends[$backend->getId()] = $backend;
		}

		$results = $this->backendMapper->getAll();
		foreach($results as $result){
			$this->assertEquals($expectedBackends[$result->getId()]->getName(), $result->getName());
			$this->assertEquals($expectedBackends[$result->getId()]->getDisplayname(), $result->getDisplayname());
			$this->assertEquals($expectedBackends[$result->getId()]->getProtocol(), $result->getProtocol());
			$this->assertEquals($expectedBackends[$result->getId()]->getId(), $result->getId());
			$this->assertEquals($expectedBackends[$result->getId()]->getEnabled(), $result->getEnabled());
		}
	}

	/**
 	 * @dataProvider backendsProvider
	 */
	public function testGetAllEnabled( array $backends){
		// Insert dummy data
		foreach ($backends as $backend){
			$this->backendMapper->insert($backend);
		}
		$results = $this->backendMapper->getAllEnabled();
		foreach($results as $key=>$result){
			$this->assertEquals(true, $result->getEnabled());
		}
	}

	public function existsProvider(){
		$backend1 = new Backend();
		$backend1->setName('foo');
		$backend1->setDisplayname('foo backend');
		$backend1->setProtocol('x-foo');
		$backend1->setEnabled(true);
		$backend2 = new Backend();
		$backend2->setName('bar');
		$backend2->setDisplayname('bar backend');
		$backend2->setProtocol('x-bar');
		$backend2->setEnabled(false);
		return array(
			array(
				$backend1,
				$backend2
			)
		);
	}

	/**
	 * @dataProvider existsProvider
	 * @param $backend1 \OCA\Chat\Db\Backend this backend must be inserted and will exist
	 * @param $backend2 \OCA\Chat\Db\Backend this backend must NOT be inserted and will NOT exist
	 */
	public function testExists(Backend $backend1, Backend $backend2){
		$this->backendMapper->insert($backend1);

		$exists = $this->backendMapper->exists($backend1->getName());
		$this->assertEquals(true, $exists);

		$exists = $this->backendMapper->exists($backend2->getName());
		$this->assertEquals(false, $exists);
	}

	/**
	 * @dataProvider backendsProvider
	 */
	public function testFindByProtocol(array $backends){
		$expectedBackends = array();
		foreach ($backends as $backend){
			$this->backendMapper->insert($backend);
			$expectedBackends[$backend->getId()] = $backend;
		}

		foreach($backends as $backend){
			$result = $this->backendMapper->findByProtocol($backend->getProtocol());
			$this->assertEquals($expectedBackends[$result->getId()]->getName(), $result->getName());
			$this->assertEquals($expectedBackends[$result->getId()]->getDisplayname(), $result->getDisplayname());
			$this->assertEquals($expectedBackends[$result->getId()]->getProtocol(), $result->getProtocol());
			$this->assertEquals($expectedBackends[$result->getId()]->getId(), $result->getId());
			$this->assertEquals($expectedBackends[$result->getId()]->getEnabled(), $result->getEnabled());

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