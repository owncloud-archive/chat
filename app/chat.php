<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

use OCA\Chat\Controller\AppController;
use OCA\Chat\Controller\OCH\ApiController;
use OCP\AppFramework\App;
use OCA\Chat\OCH\Db\ConversationMapper;
use OCA\Chat\OCH\Db\MessageMapper;
use OCA\Chat\OCH\Db\PushMessageMapper;
use OCA\Chat\OCH\Db\UserMapper;
use OCA\Chat\OCH\Db\UserOnlineMapper;
use OCA\Chat\Db\BackendMapper;
use OCA\Chat\OCH\Commands\Greet;
use OCA\Chat\OCH\Commands\Invite;
use OCA\Chat\OCH\Commands\Join;
use OCA\Chat\OCH\Commands\Offline;
use OCA\Chat\OCH\Commands\Online;
use OCA\Chat\OCH\Commands\SendChatMsg;
use OCA\Chat\OCH\Commands\StartConv;
use OCA\Chat\OCH\Commands\SyncOnline;
use OCA\Chat\OCH\Data\GetUsers;
use OCA\Chat\OCH\Data\Messages;
use OCA\Chat\OCH\Push\Get;
use OCA\Chat\OCH\Push\Delete;
use OCA\Chat\Db\Backend;
/*
 * // to prevent clashes with installed app framework versions if(!class_exists('\SimplePie')) { require_once __DIR__ . '/../3rdparty/simplepie/autoloader.php'; }
 */
class Chat extends App{

	private static $contacts;

	public $c;

	private $contactsMngr;

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
				$app
			);
		});

		$container->registerService('ApiController', function ($c) use($app) {
			return new ApiController(
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

		$container->registerService('MessageMapper', function ($c) {
			return new MessageMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('PushMessageMapper', function ($c) {
			return new PushMessageMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('UserMapper', function ($c) {
			return new UserMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('UserOnlineMapper', function ($c) {
			return new UserOnlineMapper($c->query('ServerContainer')->getDb());
		});

		$container->registerService('BackendMapper', function ($c) {
			return new BackendMapper($c->query('ServerContainer')->getDb());
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

		$container->registerService('ContactsManager', function($c){
			return $c->getServer()->getContactsManager();
		});

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
		$backendMapper = $this->c['BackendMapper'];
		if(!$backendMapper->exists($name)){
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
	 * Retrieves all contacts from the ContactsManager and parse them to a usable format.
	 * @return array Returns array with contacts, contacts as a list and contacts as an associative array
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
		$backendMapper = $this->c['BackendMapper'];
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
		$this->c = $this->getContainer();
		$backendMapper = $this->c['BackendMapper'];
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
		$cm = $this->c['ContactsManager'];
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

		$userMapper = $this->c['UserMapper'];
		$convs = $userMapper->findByUser(\OCP\User::getUser());


		$usersAllreadyInConv = array();
		$join = $this->c['JoinCommand'];
		foreach($convs as $conv){
			$users = $userMapper->findUsersInConv($conv->getConversationId());
			// Find the correct contact for the correct user
			$getMessages = $this->c['MessagesData'];
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
				"messages" => $messages
			);
			if(count($users) === 2){
				foreach($users as $user){
					if($user !== \OCP\User::getUser()){
						$usersAllreadyInConv[] = $user;
					}
				}
			}

			$join->setRequestData(array(
				"conv_id" => $conv->getConversationId(),
				"user" => $this->getCurrentUser(),
			));
			$join->execute();
		}

		$allUsers = \OCP\User::getUsers();
		$users = array_diff($allUsers, $usersAllreadyInConv);
		// $users hold the users whe doens't have a conv with

		$startConv = $this->c['StartConvCommand'];
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
					"messages" => array()
				);

			}
		}

		return $r;
	}

}
