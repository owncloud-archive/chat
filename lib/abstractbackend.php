<?php

namespace OCA\Chat;

use \OCP\IConfig;
use \OCA\Chat\App\Chat;
use \OCA\Chat\Db\ConfigMapper;

abstract class AbstractBackend implements IBackend {

	protected static $initConvs = array();

	/**
	 * @var \OCP\IConfig
	 */
	private $config;

	/**
	 * @var \OCA\Chat\Db\ConfigMapper
	 */
	private $configMapper;

	function __construct(ConfigMapper $configMapper, IConfig $config){
		$this->configMapper = $configMapper;
		$this->config = $config;
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
		return $this->config->getAppValue('chat', 'backend_' . $this->getId() .  '_enabled', true);
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