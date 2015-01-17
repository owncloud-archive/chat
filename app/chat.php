<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

use OCA\Chat\Controller\AppController;
use OCA\Chat\Controller\OCH\ApiController;
use OCA\Chat\Controller\ConfigController;
use OCA\Chat\Controller\AdminController;
use OCP\AppFramework\App;
use OCA\Chat\OCH\Db\ConversationMapper;
use OCA\Chat\OCH\Db\MessageMapper;
use OCA\Chat\OCH\Db\PushMessageMapper;
use OCA\Chat\OCH\Db\UserMapper;
use OCA\Chat\OCH\Db\UserOnlineMapper;
use OCA\Chat\OCH\Db\AttachmentMapper;
use OCA\Chat\Db\ConfigMapper;
use OCA\Chat\OCH\Commands\Greet;
use OCA\Chat\OCH\Commands\Invite;
use OCA\Chat\OCH\Commands\Join;
use OCA\Chat\OCH\Commands\Offline;
use OCA\Chat\OCH\Commands\Online;
use OCA\Chat\OCH\Commands\SendChatMsg;
use OCA\Chat\OCH\Commands\StartConv;
use OCA\Chat\OCH\Commands\SyncOnline;
use OCA\Chat\OCH\Commands\AttachFile;
use OCA\Chat\OCH\Commands\RemoveFile;
use OCA\Chat\OCH\Data\GetUsers;
use OCA\Chat\OCH\Data\Messages;
use OCA\Chat\OCH\Push\Get;
use OCA\Chat\OCH\Push\Delete;
use OCA\Chat\OCH\OCH;
use OCA\Chat\XMPP\XMPP;
use OCA\Chat\BackendManager;
use OCA\Chat\IBackend;

/**
 * Class Chat
 * @package OCA\Chat\App
 */
class Chat extends App{

	/**
	 * @var array used to cache the parsed contacts for every request
	 */
	private static $contacts;

	/**
	 * @var \OCP\AppFramework\IAppContainer
	 */
	public $c;

	/**
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = array()) {
		parent::__construct('chat', $urlParams);

		$container = $this->getContainer();
		$this->c = $container;
		$app = $this;

		/**
		 * Controllers
		 */
		$container->registerService('AppController', function ($c) use($app) {
			return new AppController(
				$c->query('AppName'),
				$c->query('Request'),
				$app,
				$c->query('ContactsManager'),
				$c->getServer()->getConfig()
			);
		});

		$container->registerService('ApiController', function ($c) use($app) {
			return new ApiController(
				$c->query('AppName'),
				$c->query('Request'),
				$app
			);
		});

		$container->registerService('ConfigController', function ($c) use($app) {
			return new ConfigController(
				$c->query('AppName'),
				$c->query('Request'),
				$app
			);
		});

		$container->registerService('AdminController', function ($c) use($app) {
			return new AdminController(
				$c->query('AppName'),
				$c->query('Request'),
				$app
			);
		});

		/**
		 * DataMappers
		 */

		$container->registerService('ConversationMapper', function ($c) {
			return new ConversationMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('ConversationMapper', function ($c) {
			return new ConversationMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('messageMapper', function ($c) {
			return new MessageMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('PushMessageMapper', function ($c) {
			return new PushMessageMapper(
				$c->query('ServerContainer')->getDb(),
				$c['UserOnlineMapper'],
				$c['UserMapper']
			);
		});

		$container->registerService('UserMapper', function ($c) {
			return new UserMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('UserOnlineMapper', function ($c) {
			return new UserOnlineMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('AttachmentMapper', function ($c) use ($app) {
			return new AttachmentMapper(
				$c->query('ServerContainer')->getDb(),
				$app
			);
		});

		$container->registerService('ConfigMapper', function ($c) use ($app) {
			return new ConfigMapper(
				$c->query('ServerContainer')->getDb(),
				$app->getUserId(),
				$c->query('ServerContainer')->getCrypto()
			);
		});

		/**
		 * Command API Requests
		 */
		$container->registerService('GreetCommand', function ($c) use($app) {
			return new Greet($app);
		});

		$container->registerService('InviteCommand', function ($c) use($app) {
			return new Invite($app);
		});

		$container->registerService('JoinCommand', function ($c) use($app) {
			return new Join($app);
		});

		$container->registerService('OfflineCommand', function ($c) use($app) {
			return new Offline($app);
		});

		$container->registerService('OnlineCommand', function ($c) use($app) {
			return new Online($app);
		});

		$container->registerService('SendChatMsgCommand', function ($c) use($app) {
			return new SendChatMsg($app);
		});

		$container->registerService('StartConvCommand', function ($c) use($app) {
			return new StartConv($app);
		});

		$container->registerService('SyncOnlineCommand', function ($c) use($app) {
			return new SyncOnline($app);
		});

		$container->registerService('AttachFileCommand', function ($c) use($app) {
			return new AttachFile($app);
		});

		$container->registerService('RemoveFileCommand', function ($c) use($app) {
			return new RemoveFile($app);
		});


		/**
		 * Push API Requests
		 */
		$container->registerService('GetPush', function ($c) use($app) {
			return new Get($app);
		});

		$container->registerService('DeletePush', function ($c) use($app) {
			return new Delete($app);
		});

		/**
		 * Data API Requests
		 */
		$container->registerService('GetUsersData', function ($c) use($app) {
			return new GetUsers($app);
		});

		$container->registerService('MessagesData', function ($c) use($app) {
			return new Messages($app);
		});

		/**
		 * Manager
		 */
		$container->registerService('ContactsManager', function($c){
			return $c->getServer()->getContactsManager();
		});

		$container->registerService('UserManager', function($c){
			return $c->getServer()->getUserManager();
		});

		$container->registerService('UserSession', function($c){
			return $c->getServer()->getUserSession();
		});

		$container->registerService('NavigationManager', function($c){
			return $c->getServer()->getNavigationManager();
		});

		$container->registerService('Config', function($c) {
			return $c->getServer()->getConfig();
		});

		$container->registerService('BackendManager', function($c){
			return new BackendManager();
		});

		$container->registerService('OCH', function($c) use ($app){
			return new OCH($app);
		});

		$container->registerService('XMPP', function($c) use ($app){
			return new XMPP($app);
		});


	}

	public function registerBackend(IBackend $backend){
		$backendManager = $this->c['BackendManager'];
		$backendManager::registerBackend($backend);
	}

	/**
	 * Retrieves all contacts from the ContactsManager and parse them to a
	 * usable format.
	 * @return array Returns array with contacts, contacts as a list and
	 * contacts as an associative array
	 */
	public function getContacts(){
		if(count(self::$contacts) == 0){
			// ***
			// the following code should be ported
			// so multiple backends are allowed
			$userOnlineMapper = $this->c['UserOnlineMapper'];
			$usersOnline = $userOnlineMapper->getOnlineUsers();
			$syncOnline = $this->c['SyncOnlineCommand'];
			$syncOnline->execute();
			// ***

			$cm = $this->c['ContactsManager'];
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
		$backendManager = $this->c['BackendManager'];
		return $backendManager->getEnabledBackends();
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
		$backendManager = $this->c['BackendManager'];

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
		$backendManager = $this->c['BackendManager'];
		$backendManager->getBackendByProtocol($protocol);

	}

	/**
	 * Get the contact of the current ownCloud user
	 * @return array
	 */
	public function getCurrentUser(){
		return $this->getUserasContact($this->c['UserSession']->getUser()->getUID());
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function getUserasContact($id){
		$result = $this->c['ContactsManager']->search($id, array('id'));
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
			$data['address_book_id'] = $addressbookKey[1];
			$data['address_book_backend'] = $addressbookKey[0];
		} else {
			$data['address_book_id'] = '';
			$data['address_book_backend'] = $addressbookKey[0];
		}
		return $data;
	}
	/**
	 * @return array
	 * @todo porting
	 */
	public function getInitConvs(){
		$backends = $this->getBackends();
		$result = array();
		foreach($backends as $backend){
			$result[$backend->getId()] = $backend->getInitConvs();
		}
		return $result;
	}

	/**
	 * @param $path path to file
	 * @return int id of the file
	 */
	public function getFileId($path){
		$userFolder = $this->c->getServer()->getUserFolder(\OCP\User::getUser());
		$file = $userFolder->get($path);
		return $file->getId();
	}

	/**
	 * @return string current ownCloud user id
	 */
	public function getUserId(){
		$user = $this->c['UserSession']->getUser();
		if (is_object($user)){
			return $user->getUID();
		} else {
			return null;
		}
	}

}
