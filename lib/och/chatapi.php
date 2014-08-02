<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH;
use \OCP\AppFramework\IAppContainer;

/*
 * API Base Class
 */
abstract class ChatAPI {

	public $app;
	protected $requestData;

	public function __construct(IAppContainer $app){
		$this->app = $app;
	}

	abstract function setRequestData(array $requestData);

	public function getRequestData(){
		return $this->requestData;
	}

	abstract public function execute();
}
