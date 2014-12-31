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
use \OCP\AppFramework\Http\TemplateResponse;
use \OCA\Chat\App\Chat;
use \OCP\Contacts\IManager;
use \OCP\IConfig;

class AppController extends Controller {

	private $app;

	private $c;

	/**
	 * @var \OCP\IConfig
	 */
	private $config;

	public function __construct($appName, IRequest $request,  Chat $app, IManager $cm, IConfig $config){
		parent::__construct($appName, $request);
		$this->app = $app;
		$this->c = $app->getContainer();
		$this->cm = $cm;
		$this->config = $config;
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 * @return TemplateResponse
	 */
	public function index() {
		session_write_close();
		$greet = $this->c['GreetCommand'];
		$greet->setRequestData(array(
			"timestamp" => time(),
			"user" => $this->app->getCurrentUser(),
		));
		$sessionId = $greet->execute();

		$contacts = $this->app->getContacts();
		$backends = $this->app->getBackends();
		$backendsToArray = array();
		foreach($backends as $backend){
			$backendsToArray[$backend->getId()] = $backend->toArray();
		}
		$initConvs = $this->app->getInitConvs();
		$params = array(
			"initvar" => json_encode(array(
				"contacts" => $contacts['contacts'],
				"contactsList" => $contacts['contactsList'],
				"contactsObj" => $contacts['contactsObj'],
				"backends" => $backendsToArray,
				"initConvs" => $initConvs,
				"sessionId" => $sessionId['session_id'], // needs porting!
			)),
			"avatars-enabled" => $this->config->getSystemValue('enable_avatars', true)
 		);
		return new TemplateResponse($this->appName, 'main', $params);
	}

	/**
	 * @NoAdminRequired
	 * @return JSONResponse
	 */
	public function contacts(){
		session_write_close();
		return new JSONResponse($this->app->getContacts());
	}

	/**
	 * @NoAdminRequired
	 * @return JSONResponse
	 */
	public function addContact($contacts){

		$addressbooks = $this->cm->getAddressBooks();
		$key = array_search('Contacts', $addressbooks);

		// Create contacts
		$ids = array();
		foreach ($contacts as $contact){
			$r = $this->cm->createOrUpdate($contact, $key);
			$ids[] = $r->getId();
		}

		// Return just created contacts as contacts which can be used by the Chat app
		$contacts =  $this->app->getContacts();
		$newContacts = array();
		foreach ($ids as $id){
			$newContacts[$id] = $contacts['contactsObj'][$id];
		}

		return $newContacts;
	}

	/**
	 * @NoAdminRequired
	 * @return JSONResponse
	 */
	public function removeContact($contacts){
		// Create contacts
		$ids = array();
		foreach ($contacts as $contact){
			$this->cm->delete($contact, 'local:1');
		}

	}

}
