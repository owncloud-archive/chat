<?php

namespace OCA\Chat\Controller;

use OCA\Chat\IBackendManager;
use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCP\IRequest;

class AdminController extends Controller {

	/**
	 * @var \OCA\Chat\BackendManager
	 */
	private $backendManager;

	public function __construct($appName, IRequest $request,  IBackendManager $backendManager){
		parent::__construct($appName, $request);
		$this->backendManager = $backendManager;
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