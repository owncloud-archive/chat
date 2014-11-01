<?php

namespace OCA\Chat\XMPP;

use \OCA\Chat\IBackend;
use \OCA\Chat\App\Chat;
use \OCA\Chat\AbstractBackend;

class XMPP extends AbstractBackend implements IBackend {

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


}