<?php

namespace OCA\Chat\Controller;

use \OCP\AppFramework\Controller;

class ConfigController extends Controller {

	private $app;

	private $c;

	/**
	 * @var \OCA\Chat\Db\ConfigMapper
	 */
	private $configMapper;

	public function __construct($appName, IRequest $request,  Chat $app){
		parent::__construct($appName, $request);
		$this->app = $app;
		$this->c = $app->getContainer();
		$this->configMapper = $this->c['ConfigMapper'];
	}

	public function set($backends){
		foreach($backends as $backend){
			foreach($backend['config'] as $key=>$value){
				$this->configMapper->set($backend['id'], $key, $value);
			}
		}
	}

}