<?php
namespace OCA\Chat\Core;

use \OCA\Chat\Db\Backend;
use \OCA\Chat\Db\BackendMapper;
use \OCP\AppFramework\IAppContainer;

class AppApi {
	
	protected $app;
	
	public function __construct(IAppContainer $app){
		$this->app = $app;
	}
	
	public function registerBackend($displayName, $name, $protocol, $enabled){
		$backendMapper = new BackendMapper($this->app->getCoreApi());
		if($backendMapper->exists($name)){
			// Only execute when there are no backends registered i.e. on first run
			$backend = new Backend();
			$backend->setDisplayname($displayName);
			$backend->setName($name);
			$backend->setProtocol($protocol);
			$backend->setEnabled($enabled);
			$backendMapper->insert($backend);
		}
	}
	
	public function getEnabledBackends(){
		$backendMapper = new BackendMapper($this->app->getCoreApi());
		return $backendMapper->getAll();
	}
}