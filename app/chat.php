<?php

namespace OCA\Chat\App;

use OCA\Chat\Core\API;
use OCA\Chat\Controller\AppController;
use OCA\Chat\Controller\OCH\ApiController;
use OCP\AppFramework\App;
use OCA\Chat\Core\AppApi;
use OCA\Chat\OCH\Db\ConversationMapper;
use OCA\Chat\OCH\Db\MessageMapper;
use OCA\Chat\OCH\Db\PushMessageMapper;
use OCA\Chat\OCH\Db\UserMapper;
use OCA\Chat\OCH\Db\UserOnlineMapper;
use OCA\Chat\Db\BackendMapper;
use OCA\Chat\OCH\Db\InitConvMapper;

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
		 * Mappers
		 */

		$container->registerService('ConversationMapper', function ($c) {
			return new ConversationMapper($c->query('API'));
		});

		$container->registerService('ConversationMapper', function ($c) {
			return new ConversationMapper($c->query('API'));
		});

		$container->registerService('MessageMapper', function ($c) {
			return new MessageMapper($c->query('API'));
		});

		$container->registerService('PushMessageMapper', function ($c) {
			return new PushMessageMapper($c->query('API'));
		});

		$container->registerService('UserMapper', function ($c) {
			return new UserMapper($c->query('API'));
		});

		$container->registerService('UserOnlineMapper', function ($c) {
			return new UserOnlineMapper($c->query('API'));
		});

		$container->registerService('InitConvMapper', function ($c) {
			return new InitConvMapper($c->query('API'));
		});

		$container->registerService('BackendMapper', function ($c) {
			return new BackendMapper($c->query('API'));
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

