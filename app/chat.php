<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

use OCA\Chat\Core\API;
use OCA\Chat\Controller\AppController;
use OCA\Chat\Controller\OCH\ApiController;
use OCA\Chat\OCH\Commands\Archive;
use OCA\Chat\OCH\Commands\UnArchive;
use OCP\AppFramework\App;
use OCA\Chat\Core\AppApi;
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
/*
 * // to prevent clashes with installed app framework versions if(!class_exists('\SimplePie')) { require_once __DIR__ . '/../3rdparty/simplepie/autoloader.php'; }
 */
class Chat extends App{

	public function __construct(array $urlParams = array()) {
		parent::__construct('chat', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('AppController', function ($c) {
			return new AppController($c->query('API'), $c->query('Request'), $c);
		});

		$container->registerService('ApiController', function ($c) {
			return new ApiController($c->query('API'), $c->query('Request'), $c);
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
		$container->registerService('GreetCommand', function ($c) {
			return new Greet($c);
		});

		$container->registerService('ArchiveCommand', function ($c) {
			return new Archive($c);
		});

		$container->registerService('UnArchiveCommand', function ($c) {
			return new UnArchive($c);
		});

		$container->registerService('InviteCommand', function ($c) {
			return new Invite($c);
		});

		$container->registerService('JoinCommand', function ($c) {
			return new Join($c);
		});

		$container->registerService('OfflineCommand', function ($c) {
			return new Offline($c);
		});

		$container->registerService('OnlineCommand', function ($c) {
			return new Online($c);
		});

		$container->registerService('SendChatMsgCommand', function ($c) {
			return new SendChatMsg($c);
		});

		$container->registerService('StartConvCommand', function ($c) {
			return new StartConv($c);
		});

		$container->registerService('SyncOnlineCommand', function ($c) {
			return new SyncOnline($c);
		});

		/**
		 * Push API Requests
		 */
		$container->registerService('GetPush', function ($c) {
			return new Get($c);
		});

		$container->registerService('DeletePush', function ($c) {
			return new Delete($c);
		});

		/**
		 * Data API Requests
		 */
		$container->registerService('GetUsersData', function ($c) {
			return new GetUsers($c);
		});

		$container->registerService('MessagesData', function ($c) {
			return new Messages($c);
		});

		/**
		* Utility
		*/
		$container->registerService('API', function ($c) {
			return new API($c->query('AppName'));
		});

		$container->registerService('AppApi', function ($c) {
			return new AppApi($c);
		});
	}

}
