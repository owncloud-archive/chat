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
use OCA\Chat\Middleware\ErrorMiddleware;

/**
 * Class Chat
 * @package OCA\Chat\App
 */
class Chat extends App{

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

	/**
	 * @var \OCP\AppFramework\IAppContainer
	 */
	private $c;

	/**
	 * @var $name of the app
	 */
	private $name = 'chat';

	public $viewType;

	/**
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = array()) {
		parent::__construct('chat', $urlParams);

		$container = $this->getContainer();
		$this->c = $container;
		$app = $this;

		$this->c['AppName'] = 'chat';
		$this->c['appName'] = 'chat';




		/**
		 * Controllers
		 */
		$container->registerService('AppController', function ($c) use($app) {
			return new AppController(
				$app->name,
				$c->query('Request'),
				$app,
				$c->query('OCP\Contacts\IManager'),
				$c->query('OCP\IConfig'),
				$c->query('GreetCommand')
			);
		});

		$container->registerService('ApiController', function ($c) use($app) {
			return new ApiController(
				$app->name,
				$c->query('Request'),
				$app
			);
		});

		$container->registerService('ConfigController', function ($c) use($app) {
			return new ConfigController(
				$app->name,
				$c->query('Request'),
				$c->query('ConfigMapper'),
				$c->query('BackendManager')
			);
		});

		$container->registerService('AdminController', function ($c) use($app) {
			return new AdminController(
				$app->name,
				$c->query('Request'),
				$c->query('BackendManager')
			);
		});

		/**
		 * DataMappers
		 */

		$container->registerService('ConversationMapper', function ($c) {
			return new ConversationMapper(
				$c->query('OCP\IDb')
			);
		});

		$container->registerService('ConversationMapper', function ($c) {
			return new ConversationMapper(
				$c->query('OCP\IDb')
			);
		});

		$container->registerService('MessageMapper', function ($c) {
			return new MessageMapper(
				$c->query('OCP\IDb')
			);
		});

		$container->registerService('PushMessageMapper', function ($c) {
			return new PushMessageMapper(
				$c->query('OCP\IDb'),
				$c['UserOnlineMapper'],
				$c['UserMapper']
			);
		});

		$container->registerService('UserMapper', function ($c) {
			return new UserMapper(
				$c->query('OCP\IDb')
			);
		});

		$container->registerService('UserOnlineMapper', function ($c) {
			return new UserOnlineMapper(
				$c->query('OCP\IDb')
			);
		});

		$container->registerService('AttachmentMapper', function ($c) use ($app) {
			return new AttachmentMapper(
				$c->query('OCP\IDb'),
				$app
			);
		});

		$container->registerService('ConfigMapper', function ($c) use ($app) {
			return new ConfigMapper(
				$c->query('OCP\IDb'),
				$app->getUserId(),
				$c->query('OCP\Security\ICrypto')
			);
		});

		/**
		 * Command API Requests
		 */
		$container->registerService('GreetCommand', function ($c) use($app) {
			return new Greet(
				$c->query('PushMessageMapper'),
				$c->query('UserOnlineMapper')
			);
		});

		$container->registerService('InviteCommand', function ($c) use($app) {
			return new Invite(
				$c->query('PushMessageMapper'),
				$c->query('JoinCommand'),
				$c->query('GetUsersData')
			);
		});

		$container->registerService('JoinCommand', function ($c) use($app) {
			return new Join(
				$c->query('PushMessageMapper'),
				$c->query('GetUsersData'),
				$c->query('UserMapper')
			);
		});

		$container->registerService('OfflineCommand', function ($c) use($app) {
			return new Offline(
				$c->query('PushMessageMapper'),
				$c->query('UserOnlineMapper'),
				$c->query('SyncOnlineCommand')
			);
		});

		$container->registerService('OnlineCommand', function ($c) use($app) {
			return new Online(
				$c->query('UserOnlineMapper'),
				$c->query('SyncOnlineCommand')
			);
		});

		$container->registerService('SendChatMsgCommand', function ($c) use($app) {
			return new SendChatMsg(
				$c->query('UserMapper'),
				$c->query('PushMessageMapper'),
				$c->query('MessageMapper')
			);
		});

		$container->registerService('StartConvCommand', function ($c) use($app) {
			return new StartConv(
				$c->query('MessageMapper'),
				$c->query('ConversationMapper'),
				$c->query('InviteCommand'),
				$c->query('JoinCommand'),
				$c->query('GetUsersData'),
				$c->query('MessagesData')
			);
		});


		$container->registerService('SyncOnlineCommand', function ($c) use($app) {
			return new SyncOnline(
				$c->query('UserOnlineMapper')
			);
		});

		$container->registerService('AttachFileCommand', function ($c) use($app) {
			return new AttachFile(
				$app,
				$c->query('UserMapper'),
				$c->query('AttachmentMapper'),
				$c->query('PushMessageMapper')
			);
		});

		$container->registerService('RemoveFileCommand', function ($c) use($app) {
			return new RemoveFile(
				$app,
				$c->query('PushMessageMapper'),
				$c->query('AttachmentMapper'),
				$c->query('UserMapper')
			);
		});


		/**
		 * Push API Requests
		 */
		$container->registerService('GetPush', function ($c) use($app) {
			return new Get(
				$c->query('PushMessageMapper')
			);
		});

		$container->registerService('DeletePush', function ($c) use($app) {
			return new Delete(
				$c->query('PushMessageMapper')
			);
		});

		/**
		 * Data API Requests
		 */
		$container->registerService('GetUsersData', function ($c) use($app) {
			return new GetUsers(
				$app,
				$c->query('UserMapper')
			);
		});

		$container->registerService('MessagesData', function ($c) use($app) {
			return new Messages(
				$c->query('MessageMapper')
			);
		});

		/**
		 * Manager
		 */
		$container->registerService('BackendManager', function($c){
			return new BackendManager();
		});

		$container->registerService('OCH', function($c) use ($app){
			return new OCH(
				$c->query('ConfigMapper'),
				$c->query('OCP\IConfig'),
				$c->query('UserMapper'),
				$c->query('AttachmentMapper'),
				$c->query('StartConvCommand'),
				$c->query('MessagesData'),
				$c->query('JoinCommand'),
				$app
			);
		});

		$container->registerService('XMPP', function($c) use ($app){
			return new XMPP(
				$c->query('ConfigMapper'),
				$c->query('OCP\IConfig'),
				$app
			);
		});

		$container->registerService('ErrorMiddleware', function($c) use ($app){
			return new ErrorMiddleware($app);
		});

		// executed in the order that it is registered
		$container->registerMiddleware('ErrorMiddleware');


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

	public function query($param){
		return $this->getContainer()->query($param);
	}

	public function registerService($name, $callback){
		return $this->getContainer()->registerService($name, $callback);
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
		if(count(self::$contacts) === 0){
			// ***
			// the following code should be ported
			// so multiple backends are allowed
			$userOnlineMapper = $this->c['UserOnlineMapper'];
			$usersOnline = $userOnlineMapper->getOnlineUsers();
			$syncOnline = $this->c['SyncOnlineCommand'];
			$syncOnline->execute();
			// ***

			$cm = $this->c->query('OCP\Contacts\IManager');
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
		$userFolder = $this->query('\OCP\IRootFolder')->getUserFolder();
		$file = $userFolder->get($path);
		return $file->getId();
	}

	/**
	 * @return string current ownCloud user id
	 */
	public function getUserId(){
		if(is_null(self::$userId)){
			$user = $this->query('OCP\IUserSession')->getUser();
			if (is_object($user)){
				self::$userId = $user->getUID();
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
