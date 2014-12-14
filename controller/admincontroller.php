<?php

namespace OCA\Chat\Controller;

use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http;
use \OCP\AppFramework\Http\TemplateResponse;



class AdminController extends Controller {

	private $app;

	private $c;

	/**
	 * @var \OCA\Chat\Db\ConfigMapper
	 */
	private $configMapper;

	public function __construct($appName, IRequest $request,  Chat $app){
		parent::__construct($appName, $request);
		$this->app = $app;
		$this->c = $app->getContainer();
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(){
		return new TemplateResponse($this->appName, 'admin', array());
	}
}