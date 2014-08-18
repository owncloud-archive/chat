<?php

namespace OCA\Chat\Db;

class DummyTest extends \PHPUnit_Framework_TestCase {

	public function testAppConfig(){
		\OCP\Config::setAppValue('chat', 'dummyValue', 'test123');

		$this->assertEquals('test123', \OCP\Config::getAppValue('chat', 'dummyValue'));
	}

}