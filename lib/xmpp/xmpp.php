<?php

namespace OCA\Chat\XMPP;

use \OCA\Chat\IBackend;
use \OCA\Chat\App\Chat;
use \OCA\Chat\AbstractBackend;
use \OCA\Chat\Db\ConfigMapper;

class XMPP extends AbstractBackend implements IBackend {

	private $app;

	public function __construct(ConfigMapper $configMapper, IConfig $config, Chat $app){
		parent::__construct($configMapper, $config);
		$this->app = $app;
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

	public function getDefaultConfig(){
		return array(
			'jid' => null,
			"password" => null,
			"bosh_url" => null
		);
	}


	public function getHelp(){
		return '';
	}
}