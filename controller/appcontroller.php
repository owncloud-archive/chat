<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\Controller;

use \OCP\AppFramework\Controller;
use \OCP\IRequest;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCA\Chat\App\Chat;
use \OCP\Contacts\IManager;
use \OCP\IConfig;
use \OCA\Chat\OCH\Commands\Greet;

class AppController extends Controller {

	/**
	 * @var Chat OCA\Chat\App\Chat;
	 */
	private $app;

	/**
	 * @var \OCP\IConfig
	 */
	private $config;

	/**
	 * @var \OCP\IManager
	 */
	private $cm;

	/**
	 * @var \OCA\Chat\OCH\Commands\Greet
	 */
	private $greet;

	public function __construct(
		$appName,
		IRequest $request,
		Chat $app,
		IManager $cm,
		IConfig $config,
		Greet $greet
	){
		parent::__construct($appName, $request);
		$this->app = $app;
		$this->cm = $cm;
		$this->config = $config;
		$this->greet = $greet;
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 * @return TemplateResponse
	 */
	public function index() {
		session_write_close();
		$this->greet->setRequestData(array(
			"timestamp" => time(),
			"user" => $this->app->getCurrentUser(),
		));
		$sessionId = $this->greet->execute();
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


	/**
	 * @NoAdminRequired
	 * @return JSONResponse
	 */
	public function initVar(){
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
		return array(
			"contacts" => $contacts['contacts'],
			"contactsList" => $contacts['contactsList'],
			"contactsObj" => $contacts['contactsObj'],
			"backends" => $backendsToArray,
			"initConvs" => $initConvs,
			"sessionId" => $sessionId['session_id'], // needs porting!
		);
	}

}
