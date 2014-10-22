<?php

namespace OCA\Chat\XMPP;

use \OCP\Chat\IBackend;

class XMPP implements IBackend {

	private static $initConvs = array();

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	private $app;

	/**
	 * @var \OCP\AppFramework\IAppContainer
	 */
	private $c;

	function __construct(Chat $app){
		$this->app = $app;
		$this->c = $app->getContainer();
	}

	public function getId(){
		return 'xmpp';
	}

	public function getInitConvs(){
		return array(
			"CONV_ID_1413235234356234645699887_56" => array(
				"id" => "CONV_ID_1413235234356234645699887_56",
				"users" => array(
					"admin",
					1
				),
				"backend" => "xmpp",
				"messages" => array(
				),
				"files" => array()
			)
		);
	}

	public function getDisplayName(){
		return 'XMPP';
	}

	public function getProtocols(){
		return array(
			'xmpp'
		);
	}

	public function hasProtocol($protocol){
		if(in_array($protocol, $this->getProtocols())){
			return true;
		} else {
			return false;
		}
	}

	public function isEnabled(){
		return true;
	}

	public function getConfig(){
		return array();
	}

	public function toArray(){
		$result = array();
		$result['id'] = $this->getId();
		$result['enabled'] = $this->isEnabled();
		$result['displayname'] = $this->getDisplayName();
		$result['protocols'] = $this->getProtocols();
		$result['config'] = $this->getConfig();
		return $result;
	}

}