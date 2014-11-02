<?php

namespace OCA\Chat;

abstract class AbstractBackend implements IBackend {

	protected static $initConvs = array();

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	protected $app;

	/**
	 * @var \OCP\AppFramework\IAppContainer
	 */
	protected $c;

	/**
	 * @var \OCA\Chat\Db\ConfigMapper
	 */
	private $configMapper;

	function __construct(Chat $app){
		$this->app = $app;
		$this->c = $app->getContainer();
		$this->configMapper = $this->c['ConfigMapper'];
	}

	public function hasProtocol($protocol){
		if(in_array($protocol, $this->getProtocols())){
			return true;
		} else {
			return false;
		}
	}

	public function toArray(){
		$result = array();
		$result['id'] = $this->getId();
		$result['enabled'] = $this->isEnabled();
		$result['displayname'] = $this->getDisplayName();
		$result['protocols'] = $this->getProtocols();
		$result['config'] = $this->getConfig();
		$result['connected'] = false;
		return $result;
	}

	public function isEnabled(){
		return \OCP\Config::getAppValue('chat', 'backend_' . $this->getId() .  '_enabled', true);
	}

	public function getConfig(){
		$config = $this->configMapper->getByBackend($this->getId());
		$defaultConfig = $this->getDefaultConfig();

		$configNotInDB = array_diff_key($defaultConfig, $config);
		foreach ($configNotInDB as $key=>$value){
			$config[$key] = $value;
		}
		return $config;
	}

}