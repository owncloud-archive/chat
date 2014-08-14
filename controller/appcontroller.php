<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\Controller;

use \OCP\AppFramework\Controller;
use \OCP\IRequest;
use \OCP\AppFramework\IAppContainer;
use \OCP\AppFramework\Http\JSONResponse;
use \OCA\Chat\Db\Backend;
use \OCA\Chat\Db\BackendMapper;
use \OCP\AppFramework\Http\TemplateResponse;


class AppController extends Controller {

	private $app;

	public function __construct($appName, IRequest $request,  IAppContainer $app){
		parent::__construct($appName, $request);
		$this->app = $app;
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 * @return TemplateResponse
	 */
	public function index() {
		session_write_close();
		$appApi = $this->app['AppApi'];

		$greet = $this->app['GreetCommand'];
		$greet->setRequestData(array(
			"timestamp" => time(),
			"user" => $appApi->getCurrentUser(),
		));
		$sessionId = $greet->execute();

		$contacts = $appApi->getContacts();
		$backends = $appApi->getBackends();
		$initConvs = $appApi->getInitConvs();


		$params = array(
			"initvar" => json_encode(array(
				"contacts" => $contacts['contacts'],
				"contactsList" => $contacts['contactsList'],
				"contactsObj" => $contacts['contactsObj'],
				"backends" => $backends,
				"initConvs" => $initConvs,
				"sessionId" => $sessionId['session_id'], // needs porting!
			))
		);
		return new TemplateResponse($this->appName, 'main', $params);
	}

	/**
	 * @param string $do
	 * @param string $backend
	 * @param int $id
	 * @return JSONResponse
	 */
	public function backend($do, $backend, $id){
		$backendMapper = new BackendMapper($this->app->getCoreApi());
		$backend = new Backend();
		$backend->setId($id);

		if($this->params('do') === 'enable'){
			$backend->setEnabled('true');
		} elseif($this->params('do') === 'disable'){
			$backend->setEnabled('false');
		}

		$backendMapper->update($backend);
		return new JSONResponse(array("status" => "success"));
	}

	/**
	 * @NoAdminRequired
	 * @return JSONResponse
	 */
	public function contacts(){
		session_write_close();
		$appApi = $this->app['AppApi'];
		$contacts = $appApi->getContacts();

		return new JSONResponse($contacts);
	}

}
