<?php

namespace OCA\Chat\Command;

use \OCP\App\IAppManager;
use \OCP\IDb;
use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

class UnInstall extends Command {

	private $appManager;

	private $db;

	public function __construct(IAppManager $appManager, IDb $db){
		$this->appManager = $appManager;
		$this->db = $db;
		parent::__construct();
	}

	public function configure(){
		$this->setName('chat:uninstall')
			->setDescription('Completely uninstalls the Chat app. WARNING: ALL Chat DATA WILL BE LOST!')
		;
	}

	public function execute(InputInterface $input, OutputInterface $output){
		$this->appManager->disableApp('chat');
		$output->writeln("Chat app disabled");
		$queries = array(
			"DROP TABLE chat_attachments;",
			"DROP TABLE chat_config;",
			"DROP TABLE chat_och_conversations;",
			"DROP TABLE chat_och_messages;",
			"DROP TABLE chat_och_push_messages;",
			"DROP TABLE chat_och_users_online;",
			"DROP TABLE chat_och_users_in_conversation;",
			"DELETE FROM appconfig WHERE appid='chat';"
		);
		foreach ($queries as $qeury) {
			$this->db->executeQuery($qeury);
		}
		$output->writeln("Database cleaned up");
		$output->writeln("Chat uninstalled");
	}
}