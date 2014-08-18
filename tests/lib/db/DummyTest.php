<?php

namespace OCA\Chat\Db;

class DummyTest extends \PHPUnit_Framework_TestCase {

	public function testAppConfig(){

		$this->assertEquals('0.2.0.2', \OCP\Config::getAppValue('chat', 'version_installed'));
	}

}