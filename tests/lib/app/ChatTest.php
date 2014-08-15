<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

class ChatTest extends \PHPUnit_Framework_TestCase {


	private $c;

	private $app;

	public function setUp(){
		$this->app = new Chat();
		$this->c = $this->app->getContainer();
	}

	public function testRegisterBackend(){

	}

}