<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

use \OCA\Chat\IBackend;
use \OCA\Chat\BackendManager;
use \OCA\Chat\OCH\Db\UserOnlineMapper;
use \OCA\Chat\OCH\Commands\SyncOnline;
use \OCP\IUser;
use \OCP\Contacts\IManager;
use \OCP\Files\IRootFolder;


/**
 * Class Chat
 * @package OCA\Chat\App
 */
class Chat {

	const APP=1;
	const INTEGRATED=2;

	/**
	 * @var array used to cache the parsed contacts for every request
	 */
	private static $contacts;

	/**
	 * @var array used to cache the parsed initConvs for every request
	 */
	private static $initConvs;

	/**
	 * @var array used to cache the user id for every request
	 */
	private static $userId;

	private $backendManager;

	private $userOnlineMapper;

	private $syncOnline;

	private $user;

	private $contactsManager;

	private $rootFolder;

	public $viewType;

	public function __construct(
		BackendManager $backendManager,
		UserOnlineMapper $userOnlineMapper,
		SyncOnline $syncOnline,
		IUser $user,
		IManager $contactsManager,
		IRootFolder $rootFolder
	) {
		$this->backendManager = $backendManager;
		$this->userOnlineMapper = $userOnlineMapper;
		$this->syncOnline = $syncOnline;
		$this->user = $user;
		$this->contactsManager = $contactsManager;
		$this->rootFolder = $rootFolder;
		$this->setViewType();
	}

	private function setViewType(){
		$requestUri = \OCP\Util::getRequestUri();
		if(substr($requestUri, -5) === 'chat/'){
			$this->viewType = self::APP;
		} else {
			$this->viewType = self::INTEGRATED;
		}
	}

	public function registerBackend(IBackend $backend){
		$backendManager = $this->backendManager;
		$backendManager::registerBackend($backend);
	}

	/**
	 * Retrieves all contacts from the ContactsManager and parse them to a
	 * usable format.
	 * @return array Returns array with contacts, contacts as a list and
	 * contacts as an associative array
	 */
	public function getContacts(){
		if(count(self::$contacts) === 0){
			// ***
			// the following code should be ported
			// so multiple backends are allowed
			$userOnlineMapper = $this->userOnlineMapper;
			$usersOnline = $userOnlineMapper->getOnlineUsers();
			$syncOnline = $this->syncOnline;
			$syncOnline->execute();
			// ***

			$cm = $this->contactsManager;
			$result = $cm->search('',array('FN'));
			$receivers = array();
			$contactList = array();
			$contactsObj = array();
			$order = 0;
			foreach ($result as $r) {
				$order++;

				$data = array();

				$contactList[] = $r['id'];

				$data['id'] = $r['id'];
				$data['online'] = in_array($r['id'], $usersOnline);
				$data['displayname'] = $r['FN'];
				$data['order'] = $order;
				$data['saved'] = true;

				if(!isset($r['EMAIL'])){
					$r['EMAIL'] = array();
				}

				if(!isset($r['IMPP'])){
					$r['IMPP'] = array();
				}
				$data['backends'] =  $this->contactBackendToBackend($r['EMAIL'], $r['IMPP']);
				$addressbookKey = explode(':', $r['addressbook-key']);
				if(count($addressbookKey) === 2){
					$data['address_book_id'] = $addressbookKey[1];
					$data['address_book_backend'] = $addressbookKey[0];
				} else {
					$data['address_book_id'] = '';
					$data['address_book_backend'] = $addressbookKey[0];
				}
				$receivers[] = $data;
				$contactsObj[$r['id']] = $data;
			}
			self::$contacts = array(
				'contacts' => $receivers,
				'contactsList' => $contactList,
				'contactsObj' => $contactsObj
			);
		}
		return self::$contacts;
	}


	/**
	 * @return array
	 */
	public function getBackends(){
		return $this->backendManager->getEnabledBackends();
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
		$backendManager = $this->backendManager;

		if(is_array($emails)){
			$backend = array();
			$backend['id'] = 'email';
			$backend['displayname'] = 'E-mail';
			$backend['protocol'] = 'email';
			$backend['namespace'] = ' email';
			$backend['value'] = array($emails);
			$backends[] = $backend;
		}

		if(isset($impps)){
			foreach($impps as $impp){
				$backend = array();
				$exploded = explode(":", $impp);
				if(!isset($exploded[1])){
					// protocol not provided -> xmpp
					$info = $backendManager->getBackendByProtocol('xmpp');
					$value = $exploded[0];
					$protocol = 'xmpp';
				} else {
					$info = $backendManager->getBackendByProtocol($exploded[0]);
					$value = $exploded[1];
					$protocol = $exploded[0];
				}
				$backend['id'] = $info->getId();
				$backend['displayname'] = $info->getDisplayName();
				$backend['protocol'] = $protocol;
				$backend['namespace'] = $info->getId();
				$backend['value'] = $value ;
				$backends[] = $backend;
			}
		}

		return $backends;
	}

	/**
	 * @param string $protocol
	 * @return \OCA\Chat\IBackend
	 */
	private function getBackend($protocol){
		$this->backendManager->getBackendByProtocol($protocol);
	}

	/**
	 * Get the contact of the current ownCloud user
	 * @return array
	 */
	public function getCurrentUser(){
		return $this->getUserasContact($this->getUserId());
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function getUserasContact($id){
		if(count(self::$contacts) === 0) {
			$this->getContacts();
		}
		return self::$contacts['contactsObj'][$id];
	}
	/**
	 * @return array
	 * @todo porting
	 */
	public function getInitConvs(){
		if(count(self::$initConvs) === 0) {
			$backends = $this->getBackends();
			$result = array();
			foreach($backends as $backend){
				$result[$backend->getId()] = $backend->getInitConvs();
			}
			self::$initConvs = $result;
		}
		return self::$initConvs;
	}

	/**
	 * @param $path path to file
	 * @return int id of the file
	 */
	public function getFileId($path){
		$userFolder = $this->rootFolder->getUserFolder();
		$file = $userFolder->get($path);
		return $file->getId();
	}

	/**
	 * @return string current ownCloud user id
	 */
	public function getUserId(){
		if(is_null(self::$userId)){
			$user = $this->user ;
			if (is_object($user)){
				self::$userId = $this->user->getUID();
			} else {
				self::$userId = null;
			}
		}
		return self::$userId;

	}


	public function registerExceptionHandler(){
		set_exception_handler(function(\Exception $e){
			$this->exceptionHandler($e);
		});

	}

	private static $errors;

	public function exceptionHandler(\Exception $e){
		self::$errors = [
			0 => [
				"check" => function($msg) {
					if (substr($msg, 0, 17) === 'js file not found') {
						return true;
					}
					if (substr($msg, 0, 18) === 'css file not found') {
						return true;
					}
					return false;
				},
				"brief" => 'JS or CSS files not generated',
				"info" =>  <<<INFO
	There are two options to solve this problem: <br>
		1. generate them yourself <br>
		2. download packaged Chat app

	Click the "more information" button for more information.
INFO
				,"link" => "https://github.com/owncloud/chat#install"

			],
			1 => [
				"check" => function($msg){
					if (substr($msg, 0, 23) === '[404] Contact not found') {
						return true;
					}
				},
				"brief" => "Contact app failed to load some contacts",
				"info" => <<<INFO
	This is a bug in the Contacts app, which is fixed in the latest version of ownCloud and the Contacts app.<br>
	Please open an issue if this issue keeps occurring after updating the Contacts app.
INFO
				,"link" => ""
			],
			2 => [
				"check" => function($msg){
					if(\OCP\App::isEnabled('user_ldap')){
						return true;
					}
				},
				"brief" => "Chat app doens't work with user_ldap enabled",
				"info" => <<<INFO
	There is an bug in core with user_ldap. Therefore the Chat app can't be used. This bug is solved in the latest version of the Chat app.
INFO
				,"link" => ""
			]


		];
		foreach (self::$errors as $possibleError) {
			if($possibleError['check']($e->getMessage())){
				$brief = $possibleError["brief"];
				$info = $possibleError["info"];
				$link = $possibleError["link"];
				$raw = $e->getMessage();
			}
		}
		$version = \OCP\App::getAppVersion('chat');
		$requesttoken = \OC::$server->getSession()->get('requesttoken');


		include(__DIR__ . "/../templates/error.php");
		die();
	}

}
