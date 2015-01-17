<?php

namespace OCA\Chat\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OCA\Chat\BackendNotFoundException;
use \OCA\CHat\IBackendManager;


class EnableBackend extends Command {

	/**
	 * @var BackendManager OCA\Chat\IBackendManager
	 */
	private $backendManager;


	public function __construct(IBackendManager $backendManager){
		$this->backendManager = $backendManager;
		parent::__construct();
	}
	
	public function configure(){
		$this->setName('chat-backend:enable')
			->setDescription('Enable a specific backend')
			->addArgument(
				'backend',
				InputArgument::REQUIRED,
				'The id of the backend which you want to enable'
				)
			;
	}

	public function execute(InputInterface $input, OutputInterface $output){
		$backend = $input->getArgument('backend');
		try {
			$this->backendManager->enableBackend($backend);
			$output->writeln("Chat Backend '". $backend . "' is enabled.");
		} Catch (BackendNotFoundException $e) {
			$output->writeln("<error>Chat Backend does not exists.</error>");
		}
	}
	
}