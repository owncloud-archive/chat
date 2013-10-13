<?php

namespace OCA\Chat\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

require '/var/www/owncloud/apps/chat/server/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

use OCP\User;
use OCA\Chat\Server\DefaultWebsocket;


class Boot extends Command {

	private $userManager;

	public function __construct() {
		parent::__construct();
	}

	protected function configure() {
		$this
		->setName('chat:boot')
		->setDescription('Boot the default chat server')
		;
		// Later we can add port argument
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('Chat Boot process started via owncloud console');
		$server = IoServer::factory(
				new WsServer(
						new DefaultWebsocket(new user)
				)
				, 8080
		);
		
		$server->run();
		$output->writeln(getmypid());
	}
}