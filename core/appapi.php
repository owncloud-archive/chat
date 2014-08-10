<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

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

			$result = $cm->search('',array('id'));
			$receivers = array();
			$contactList = array();
			$contactsObj = array();
			foreach ($result as $r) {
				$data = array();

				$contactList[] = $r['id'];

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
				$addressbookKey = explode(':', $r['addressbook-key']);
				if(count($addressbookKey) === 2){
					$data['address_book_id'] = $addressbookKey[0];
					$data['address_book_backend'] = $addressbookKey[1];
				} else {
					$data['address_book_id'] = $addressbookKey[0];
					$data['address_book_backend'] = '';
				}
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
	 * @param array $impps
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
	private function contactBackendToBackend(array $emails=array(), array $impps=array()){
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

		if(isset($impps)){
			foreach($impps as $impp){
				$backend = array();
				$exploded = explode(":", $impp);
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
		return $this->getUserasContact(\OCP\User::getUser());
	}

	public function getUserasContact($id){
		$cm = \OC::$server->getContactsManager();
		// The API is not active -> nothing to do

		$result = $cm->search($id, array('id'));
		// Finding the correct result
		foreach($result as $contact){
			if($contact['id'] ===  $id){
				$r = $contact;
			}
		}
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
		$addressbookKey = explode(':', $r['addressbook-key']);
		if(count($addressbookKey) === 2){
			$data['address_book_id'] = $addressbookKey[0];
			$data['address_book_backend'] = $addressbookKey[1];
		} else {
			$data['address_book_id'] = $addressbookKey[0];
			$data['address_book_backend'] = '';
		}
		return $data;
	}
	/**
	 * @return array
	 * @todo porting
	 */
	public function getInitConvs(){
		$r = array();

		$userMapper = $this->app['UserMapper'];
		$convs = $userMapper->findByUser(\OCP\User::getUser());


		$usersAllreadyInConv = array();
		foreach($convs as $conv){
			$users = $userMapper->findUsersInConv($conv->getConversationId());
			// Find the correct contact for the correct user
			$getMessages = $this->app['MessagesData'];
			$getMessages->setRequestData(array(
				"conv_id" => $conv->getConversationId(),
				'user' => $this->getCurrentUser()
			));
			$messages = $getMessages->execute();
			$messages = $messages['messages'];
			$r['och'][$conv->getConversationId()] = array(
				"id" => $conv->getConversationId(),
				"users"=> $users,
				"backend" => "och",
				"archived" => (bool)$conv->getArchived(),
				"messages" => $messages
			);
			if(count($users) === 2){
				foreach($users as $user){
					if($user !== \OCP\User::getUser()){
						$usersAllreadyInConv[] = $user;
					}
				}
			}
		}

		$allUsers = \OCP\User::getUsers();
		$users = array_diff($allUsers, $usersAllreadyInConv);
		// $users hold the users whe doens't have a conv with

		$startConv = $this->app['StartConvCommand'];
		foreach($users as $user){
			if($user !== \OCP\User::getUser()){
				$startConv->setRequestData(array(
					"user" => $this->getCurrentUser(),
					"user_to_invite" => array(
						$this->getUserasContact($user),
					)
				));
				$info =  $startConv->execute();
				$r['och'][$info['conv_id']] = array(
					"id" => $info['conv_id'],
					"users"=> array(
						\OCP\User::getUser(),
						$user
					),
					"backend" => "och",
					"archived" => (bool)false,
					"messages" => array()
				);

			}
		}

		return $r;
	}

}
