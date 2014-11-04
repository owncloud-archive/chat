<?php

namespace OCA\Chat\Controller;

use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http;


class ConfigController extends Controller {

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
		$this->configMapper = $this->c['ConfigMapper'];
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
}