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
		$contacts = $this->app->getContacts();
		$contacts = $contacts['contacts'];
		$currentUser = $this->app->getCurrentUser();
		$currentUserId = $currentUser['id'];
		$initConvs = array();
		foreach($contacts as $contact) {
			foreach ($contact['backends'] as $backend) {
				if ($backend['id'] === 'xmpp') {
					$jid = $backend['value'];
					$initConvs[$jid] = array(
						"id" => $jid,
						"users" => array(
							$contact['id'],
							$currentUserId
						),
						"backend" => 'xmpp',
						"messages" => array(),
						"files" => array()
					);
				}
			}
		}
		return $initConvs;
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