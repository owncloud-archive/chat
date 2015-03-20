<?php

namespace OCA\Chat\OCH;

use \OCA\Chat\IBackend;
use \OCA\Chat\App\Chat;
use \OCA\Chat\AbstractBackend;
use \OCA\Chat\OCH\Db\UserMapper;
use \OCA\Chat\Db\ConfigMapper;
use \OCA\Chat\OCH\Db\AttachmentMapper;
use \OCP\IConfig;
use \OCA\Chat\OCH\Commands\StartConv;
use \OCA\Chat\OCH\Commands\Join;
use \OCA\Chat\OCH\Data\Messages;

class OCH extends AbstractBackend implements IBackend {

	/**
	 * @var $userMapper \OCA\Chat\OCH\Db\UserMapper
	 */
	private $userMapper;

	/**
	 * @var $attachmentMapper \OCA\Chat\OCH\Db\AttachmentMapper
	 */
	private $attachmentMapper;

	/**
	 * @var $messages \OCA\Chat\OCH\Data\Messages
	 */
	private $messages;

	/**
	 * @var $startconv \OCA\Chat\OCH\Commands\StartConv
	 */
	private $startconv;

	/**
	 * @var $join \OCA\Chat\OCH\Commands\Join
	 */
	private $join;

	/**
	 * @var $app \OCA\Chat\App\Chat
	 */
	private $app;

	public function __construct(
		ConfigMapper $configMapper,
		IConfig $config,
		UserMapper $userMapper,
		AttachmentMapper $attachmentMapper,
		StartConv $startconv,
		Messages $messages,
		Join $join,
		Chat $app
	){
		parent::__construct($configMapper, $config);
		$this->userMapper = $userMapper;
		$this->attachmentMapper = $attachmentMapper;
		$this->startconv = $startconv;
		$this->messages = $messages;
		$this->join = $join;
		$this->app = $app;
	}

	public function getId(){
		return 'och';
	}

	public function getInitConvs(){
		if(count(self::$initConvs) === 0){
			$this->createInitConvs();
		}
		return self::$initConvs;
	}

	private function createInitConvs(){
		$initConvs = array();
		$convs = $this->userMapper->findByUser($this->app->getUserId());
		$usersAllreadyInConv = array();
		foreach($convs as $conv){
			$users = $this->userMapper->findUsersInConv($conv->getConversationId());
			// Find the correct contact for the correct user
			$this->messages->setRequestData(array(
				"conv_id" => $conv->getConversationId(),
				'user' => $this->app->getCurrentUser(),
				"limit" => array(0,30)
			));
			$messages = $this->messages->execute();
			$messages = $messages['messages'];

			$files = $this->attachmentMapper->findRawByConv($conv->getConversationId());
			$initConvs[$conv->getConversationId()] = array(
				"id" => $conv->getConversationId(),
				"users"=> $users,
				"backend" => "och",
				"messages" => $messages,
				"files" => $files
			);
			if(count($users) === 2){
				foreach($users as $user){
					if($user !== \OCP\User::getUser()){
						$usersAllreadyInConv[] = $user;
					}
				}
			}

			$this->join->setRequestData(array(
				"conv_id" => $conv->getConversationId(),
				"user" => $this->app->getCurrentUser(),
			));
			$this->join->execute();
		}

		$allUsers = \OCP\User::getUsers();
		$users = array_diff($allUsers, $usersAllreadyInConv);

		foreach($users as $user){
			if($user !== \OCP\User::getUser()){
				$this->startconv->setRequestData(array(
					"user" => $this->app->getCurrentUser(),
					"user_to_invite" => array(
						$this->app->getUserasContact($user),
					)
				));
				$info =  $this->startconv->execute();
				$initConvs[$info['conv_id']] = array(
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
		self::$initConvs = $initConvs;
	}

	public function getDisplayName(){
		return 'ownCloud Chat';
	}

	public function getProtocols(){
		return array('x-owncloud-handle');
	}


	public function getDefaultConfig(){
		return array(
		);
	}

	public function getHelp(){
		return 'This Chat backend works without configuration. It can be used to chat with other ownCloud users';
	}
}
