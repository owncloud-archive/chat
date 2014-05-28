<?php
namespace OCA\Chat\Core;

use \OCA\Chat\Db\Backend;
use \OCP\AppFramework\IAppContainer;
use \OCA\Chat\OCH\Commands\SyncOnline;

class AppApi {

	/**
	 *
	 * @var IAppContainer
	 */
	protected $app;

	/**
	 *
	 * @var array to cache all contacts
	 */
	private static $contacts = array();

	public function __construct(IAppContainer $app){
		$this->app = $app;
	}

	/**
	 * Register a backend, so it's automatically added to the DB.
	 * Registering must be placed in the appinfo/app.php file.
	 * @param string $displayName
	 * @param string $name
	 * @param string $protocol
	 * @param string $enabled
	 */
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

	/**
	 * Get all backends which are enabled
	 * @return array
	 */
	public function getEnabledBackends(){
		$backendMapper = $this->app['BackendMapper'];
		return $backendMapper->getAll();
	}

	/**
	 * Retrieves all contacts from the ContactsManager and parse them to a usable format.
	 * @return array Returns array with contacts, contacts as a list and contacts as an associative array
	 */
	public function getContacts(){
		// ***
		// the following code should be ported
		// so multiple backends are allowed
		$userOnlineMapper = $this->app['UserOnlineMapper'];
		$usersOnline = $userOnlineMapper->getOnlineUsers();
		$syncOnline = $this->app['SyncOnlineCommand'];
		$syncOnline->execute();
		// ***

		if(count(self::$contacts) == 0){
			$cm = \OC::$server->getContactsManager();

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

				if(!isset($r['EMAIL'])){
					$r['EMAIL'] = array();
				}

				if(!isset($r['IMPP'])){
					$r['IMPP'] = array();
				}
				$data['backends'] =  $this->contactBackendToBackend($r['EMAIL'], $r['IMPP']);
				list($addressBookBackend, $addressBookId) = explode(':', $r['addressbook-key']);
				$data['address_book_id'] = $addressBookId;
				$data['address_book_backend'] = $addressBookBackend;
				$receivers[] = $data;
				$contactsObj[$r['id']] = $data;
			}
			self::$contacts = array('contacts' => $receivers, 'contactsList' => $contactList, 'contactsObj' => $contactsObj);
		}
		return self::$contacts;
	}

	/**
	 * @todo
	 */
	public function getBackends(){
		$backendMapper = $this->app['BackendMapper'];
		$backends = $backendMapper->getAllEnabled();

		$result = array();
		foreach($backends as $backend){
			$result[$backend->getName()] = $backend;
		}

		return $result;
	}

	/**
	 * Parse the emails and IMPPS properties stored in the contacts app to
	 * a format that can be used in the Chat client.
	 * @param array $emails
	 * @param array $IMPPS
	 * @return array
	 * @example of return value parsed to JSOn
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
	private function contactBackendToBackend(array $emails=array(), array $IMPPS=array()){
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

	/**
	 * Get Metadata from a backend
	 * @param string $protocol
	 * @return array
	 */
	private function getBackendInfo($protocol){
		$backendMapper = $this->app['BackendMapper'];
		$backend = $backendMapper->findByProtocol($protocol);
		$info = array();
		$info['displayname'] = $backend->getDisplayname();
		$info['namespace'] = $backend->getName(); // TODO change name to namespace
		return $info;
	}

	/**
	 * Get the contact of the current ownCloud user
	 * @return array
	 */
	public function getCurrentUser(){
		$cm = \OC::$server->getContactsManager();
		// The API is not active -> nothing to do

		$result = $cm->search(\OCP\User::getUser(), array('FN'));
		$r = $result[0];
		$data = array();
		$data['id'] = $r['id'];
		$data['displayname'] = $r['FN'];
		if(!isset($r['EMAIL'])){
			$r['EMAIL'] = array();
		}

		if(!isset($r['IMPP'])){
			$r['IMPP'] = array();
		}
		$data['backends'] =  $this->contactBackendToBackend($r['EMAIL'], $r['IMPP']);
		list($addressBookBackend, $addressBookId) = explode(':', $r['addressbook-key']);
		$data['address_book_id'] = $addressBookId;
		$data['address_book_backend'] = $addressBookBackend;
		return $data;
	}

	/**
	 * @return array
	 * @todo porting
	 */
	public function getInitConvs(){
		$r = array();

		$initConvMapper = $this->app['InitConvMapper'];
		$userMapper = $this->app['UserMapper'];
		$convs = $initConvMapper->findByUser(\OCP\User::getUser());
		
		foreach($convs as $conv){
			$users = $userMapper->findUsersInConv($conv->getConvId());
			// Find the correct contact for the correct user
			$r['och'][$conv->getConvId()] = array("id" => $conv->getConvId(), "users"=> $users, "backend" => "och");
		}
		return $r;
	}

}
