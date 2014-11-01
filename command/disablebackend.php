<?php

namespace OCA\Chat\Command;

use OCP\AppFramework\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DisableBackend extends Command {

	private $app;

	public function __construct(App $app){
		$this->app = $app;
		parent::__construct();
	}
	
	public function configure(){
		$this->setName('chat-backend:disable')
			->setDescription('Disable a specific backend')
			->addArgument(
				'backend',
				InputArgument::REQUIRED,
				'The id of the backend which you want to disable'
				)
			;
	}

	public function execute(InputInterface $input, OutputInterface $output){
		$backend = $input->getArgument('backend');
		$backendManager = $this->app->c['BackendManager'];
		$backends = $backendManager->getBackends();

		// Check if backend exits
		if(array_key_exists($backend,$backends)){
			\OCP\Config::setAppValue('chat', 'backend_' . $backend . '_enabled', false);
			$output->writeln("Chat Backend '". $backend . "' is disabled.");
		} else {
			$output->writeln("<error>Chat Backend does not exists.</error>");
		}
	}
	
}