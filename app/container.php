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
use OCA\Chat\OCH\Commands\SaveLastConv;
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
class Container extends App{

	/**
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = array()) {
		parent::__construct('chat', $urlParams);
		$container = $this->getContainer();
		$container->registerMiddleware('ErrorMiddleware');

		/**
		 * Chat Class
		 */
		$container->registerService('Chat', function($c){
			return new Chat(
				$c->query('BackendManager'),
				$c->query('UserOnlineMapper'),
				$c->query('SyncOnlineCommand'),
				$c->query('OCP\IUserSession')->getUser(),
				$c->query('OCP\Contacts\IManager'),
				$c->query('OCP\Files\IRootFolder')
			);
		});

		/**
		 * Controllers
		 */
		$container->registerService('AppController', function ($c) {
			return new AppController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('Chat'),
				$c->query('OCP\Contacts\IManager'),
				$c->query('OCP\IConfig'),
				$c->query('GreetCommand'),
				$c->query('ConfigMapper')
			);
		});

		$container->registerService('ApiController', function ($c) {
			return new ApiController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('Chat'),
				$this
			);
		});

		$container->registerService('ConfigController', function ($c) {
			return new ConfigController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('ConfigMapper'),
				$c->query('BackendManager')
			);
		});

		$container->registerService('AdminController', function ($c) {
			return new AdminController(
				$c->query('AppName'),
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

		$container->registerService('AttachmentMapper', function ($c) {
			return new AttachmentMapper(
				$c->query('OCP\IDb'),
				$c->query('Chat')
			);
		});

		$container->registerService('ConfigMapper', function ($c) {
			return new ConfigMapper(
				$c->query('OCP\IDb'),
				$c->query('Chat')->getUserId(),
				$c->query('OCP\Security\ICrypto')
			);
		});

		/**
		 * Command API Requests
		 */
		$container->registerService('GreetCommand', function ($c) {
			return new Greet(
				$c->query('PushMessageMapper'),
				$c->query('UserOnlineMapper')
			);
		});

		$container->registerService('InviteCommand', function ($c) {
			return new Invite(
				$c->query('PushMessageMapper'),
				$c->query('JoinCommand'),
				$c->query('GetUsersData')
			);
		});

		$container->registerService('JoinCommand', function ($c) {
			return new Join(
				$c->query('PushMessageMapper'),
				$c->query('GetUsersData'),
				$c->query('UserMapper')
			);
		});

		$container->registerService('OfflineCommand', function ($c) {
			return new Offline(
				$c->query('PushMessageMapper'),
				$c->query('UserOnlineMapper'),
				$c->query('SyncOnlineCommand')
			);
		});

		$container->registerService('OnlineCommand', function ($c) {
			return new Online(
				$c->query('UserOnlineMapper'),
				$c->query('SyncOnlineCommand')
			);
		});

		$container->registerService('SendChatMsgCommand', function ($c) {
			return new SendChatMsg(
				$c->query('UserMapper'),
				$c->query('PushMessageMapper'),
				$c->query('MessageMapper')
			);
		});

		$container->registerService('StartConvCommand', function ($c) {
			return new StartConv(
				$c->query('MessageMapper'),
				$c->query('ConversationMapper'),
				$c->query('InviteCommand'),
				$c->query('JoinCommand'),
				$c->query('GetUsersData'),
				$c->query('MessagesData')
			);
		});


		$container->registerService('SyncOnlineCommand', function ($c) {
			return new SyncOnline(
				$c->query('UserOnlineMapper')
			);
		});

		$container->registerService('AttachFileCommand', function ($c) {
			return new AttachFile(
				$c->query('Chat'),
				$c->query('UserMapper'),
				$c->query('AttachmentMapper'),
				$c->query('PushMessageMapper')
			);
		});

		$container->registerService('RemoveFileCommand', function ($c) {
			return new RemoveFile(
				$c->query('Chat'),
				$c->query('PushMessageMapper'),
				$c->query('AttachmentMapper'),
				$c->query('UserMapper')
			);
		});

		$container->registerService('SaveLastConvCommand', function ($c) {
			return new SaveLastConv(
				$c->query('ConfigMapper')
			);
		});


		/**
		 * Push API Requests
		 */
		$container->registerService('GetPush', function ($c) {
			return new Get(
				$c->query('PushMessageMapper')
			);
		});

		$container->registerService('DeletePush', function ($c) {
			return new Delete(
				$c->query('PushMessageMapper')
			);
		});

		/**
		 * Data API Requests
		 */
		$container->registerService('GetUsersData', function ($c) {
			return new GetUsers(
				$c->query('Chat'),
				$c->query('UserMapper')
			);
		});

		$container->registerService('MessagesData', function ($c) {
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

		$container->registerService('OCH', function($c){
			return new OCH(
				$c->query('ConfigMapper'),
				$c->query('OCP\IConfig'),
				$c->query('UserMapper'),
				$c->query('AttachmentMapper'),
				$c->query('StartConvCommand'),
				$c->query('MessagesData'),
				$c->query('JoinCommand'),
				$c->query('Chat')
			);
		});

		$container->registerService('XMPP', function($c){
			return new XMPP(
				$c->query('ConfigMapper'),
				$c->query('OCP\IConfig'),
				$c->query('Chat')
			);
		});

		$container->registerService('ErrorMiddleware', function($c){
			return new ErrorMiddleware($c->query('Chat'));
		});

	}

	public function query($param){
		return $this->getContainer()->query($param);
	}

}
