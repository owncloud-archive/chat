<?php

namespace OCA\Chat\Controller;

use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http;
use \OCP\IRequest;
use \OCA\Chat\App\Chat;


class ConfigController extends Controller {

	private $app;

	private $c;

	/**
	 * @var \OCA\Chat\Db\ConfigMapper
	 */
	private $configMapper;

	/**
	 * @var \OCA\Chat\BackendManager
	 */
	private $backendManager;

	public function __construct($appName, IRequest $request,  Chat $app){
		parent::__construct($appName, $request);
		$this->app = $app;
		$this->c = $app->getContainer();
		$this->configMapper = $this->c['ConfigMapper'];
		$this->backendManager = $this->c['BackendManager'];
	}

	/**
	 * @NoAdminRequired
	 * @param $backends
	 * @return JSONResponse
	 */
	public function set($backends){
		foreach($backends as $backend){
			foreach($backend['config'] as $key=>$value){
				$this->configMapper->set($backend['id'], $key, $value);
			}
		}

		$res = new JSONResponse();
		$res->setStatus(Http::STATUS_OK);
		return $res;
	}

	/**
	 * @param $id
	 * @return JSONResponse
	 */
	public function enableBackend($id){
		try {
			$this->backendManager->enableBackend($id);
			return new JSONResponse(array("status" => "success"));
		} Catch (BackendNotFoundException $e) {
			return new JSONResponse(array("status" => "error", "msg" => 404));
		}
	}

	/**
	 * @param $id
	 * @return JSONResponse
	 */
	public function disableBackend($id){
		try {
			$this->backendManager->disableBackend($id);
			return new JSONResponse(array("status" => "success"));
		} Catch (BackendNotFoundException $e) {
			return new JSONResponse(array("status" => "error", "msg" => 404));
		}
	}
}