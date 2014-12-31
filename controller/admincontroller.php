<?php

namespace OCA\Chat\Controller;

use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCA\Chat\App\Chat;
use \OCP\IRequest;

class AdminController extends Controller {

	private $app;

	private $c;

	/**
	 * @var \OCA\Chat\BackendManager
	 */
	private $backendManager;

	public function __construct($appName, IRequest $request,  Chat $app){
		parent::__construct($appName, $request);
		$this->app = $app;
		$this->c = $app->getContainer();
		$this->backendManager = $this->c['BackendManager'];
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(){
		$params = array();
		$params['backends'] = $this->backendManager->getBackends();
		return new TemplateResponse($this->appName, 'admin', $params, 'blank');
	}
}