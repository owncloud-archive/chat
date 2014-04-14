<?php
namespace OCA\Chat\Core;

use \OCA\Chat\Db\Backend;
use \OCP\AppFramework\IAppContainer;
use \OCA\Chat\OCH\Commands\SyncOnline;

class AppApi {

	protected $app;
	private static $contacts = array();

	public function __construct(IAppContainer $app){
		$this->app = $app;
	}

	public function registerBackend($displayName, $name, $protocol, $enabled){
		$backendMapper = $this->app['BackendMapper'];
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
		$backendMapper = $this->app['BackendMapper'];
		return $backendMapper->getAll();
	}

	public function getContacts(){
		// ***
		// the following code should be ported
		// so multiple backends are allowed
		$userOnlineMapper = $this->app['UserOnlineMapper'];
		$usersOnline = $userOnlineMapper->getOnlineUsers();
		$syncOnline = new SyncOnline($this->app);
		$syncOnline->execute();
		// ***

		if(count(self::$contacts) == 0){
			$cm = \OC::$server->getContactsManager();
			// The API is not active -> nothing to do
			if (!$cm->isEnabled()) {
				$receivers = null;
				$error = 'Please enable the contacts app.';
			}

			$result = $cm->search('',array('FN'));
			$receivers = array();
			$contactList = array();
			$contactsObj = array();
			foreach ($result as $r) {
				$data = array();

				$contactList[] = $r['FN'];

				$data['id'] = $r['id'];
				$data['online'] = in_array($r['id'], $usersOnline);
				$data['displayname'] = $r['FN'];

				$data['backends'] =  $this->contactBackendToBackend($r['EMAIL'], $r['IMPP']);
				list($addressBookBackend, $addressBookId) = explode(':', $r['key']);
				$data['address_book_id'] = $addressBookId;
				$data['address_book_backend'] = $addressBookBackend;
				$receivers[] = $data;
				$contactsObj[$r['id']] = $data;
			}
			self::$contacts = array('contacts' => $receivers, 'contactsList' => $contactList, 'contactsObj' => $contactsObj);
		}
		return self::$contacts;
	}

	public function getBackends(){
		$backendMapper = $this->app['BackendMapper'];
		$backends = $backendMapper->getAllEnabled();

		$result = array();
		foreach($backends as $backend){
			$result[$backend->getName()] = $backend;
		}

		return $result;
	}

	private function contactBackendToBackend($emails, $IMPPS){
		/*
		 * backends : [
		 *   0 : {
		 *     id : 0,1,2
		 *     displayname : "ownCloud Handle",
		 *     protocol : "x-owncloud-handle" ,
		 *     namespace : "och",
		 *     value : "derp" // i.e. the owncloud username
		 *   },
		 *   1 {
		 *     id : null,
		 *     displayname : "E-mail",
		 *     protocl : "email",
		 *     namespace : "email",
		 *     value : "name@domain.tld"
		 *   }
		 * ]
		 */
		$backends = array();

		if(is_array($emails)){
			$backend = array();
			$backend['id'] = null;
			$backend['displayname'] = 'E-mail';
			$backend['protocol'] = 'email';
			$backend['namespace'] = ' email';
			$backend['value'] = array($emails);
			$backends['email'] = $backend;
		}

		if(isset($IMPPS)){
			foreach($IMPPS as $IMPP){
				$backend = array();
				$exploded = explode(":", $IMPP);
				$info = $this->getBackendInfo($exploded[0]);
				$backend['id'] = null;
				$backend['displayname'] = $info['displayname'];
				$backend['protocol'] = $exploded[0];
				$backend['namespace'] = $info['namespace'];
				$backend['value'] = $exploded[1];
				$backends[$info['namespace']] = $backend;
			}
		}

		return $backends;
	}

	private function getBackendInfo($protocol){
		$backendMapper = $this->app['BackendMapper'];
		$backend = $backendMapper->findByProtocol($protocol);
		$info = array();
		$info['displayname'] = $backend->getDisplayname();
		$info['namespace'] = $backend->getName(); // TODO change name to namespace
		return $info;
	}

	public function getCurrentUser(){
		$cm = \OC::$server->getContactsManager();
		// The API is not active -> nothing to do
		if (!$cm->isEnabled()) {
			$receivers = null;
			$error = 'Please enable the contacts app.';
		}

		$result = $cm->search(\OCP\User::getDisplayName(), array('FN'));
		list($addressBookBackend, $addressBookId) = explode(':', $result['key']);
		$r = $result[0];
		$data = array();
		$data['id'] = $r['id'];
		$data['displayname'] = $r['FN'];
		$data['backends'] =  $this->contactBackendToBackend($r['EMAIL'], $r['IMPP']);
		$data['address_book_id'] = $addressBookId;
		$data['address_book_backend'] = $addressBookBackend;
		return $data;
	}

	public function getInitConvs(){
		$r = array();

		$userMapper = $this->app['UserMapper'];
		$convs = $userMapper->findConvsIdByUser(\OCP\User::getUser());
		$contacts = $this->getContacts();
		foreach($convs as $conv){
			$users = $userMapper->findUsersInConv($conv);
			// Find the correct contact for the correct user
			$r['och'][$conv] = array("id" => $conv, "users"=> $users, "backend" => "och");
		}
		return $r;
	}

}
