<?php
/**
 * Copyright (c) 2014, Tobia De Koninck <hey@ledfan.be>
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\Core;

use \OCP\AppFramework\IApi;

class API implements IApi {
	private $appName;

	/**
	 * constructor
	 *
	 * @param string $appName the name of your application
	 */
	public function __construct($appName) {
		$this->appName = $appName;
	}

	/**
	 * Gets the userid of the current user
	 *
	 * @return string the user id of the current user
	 */
	public function getUserId() {
		return \OCP\User::getUser ();
	}

	/**
	 * Adds a new javascript file
	 *
	 * @param string $scriptName the name of the javascript in js/ without the suffix
	 * @param string $appName the name of the app, defaults to the current one
	 */
	public function addScript($scriptName, $appName = null) {
		if ($appName === null) {
			$appName = $this->appName;
		}
		\OCP\Util::addScript ( $appName, $scriptName );
	}

	/**
	 * Adds a new css file
	 *
	 * @param string $styleName the name of the css file in css/without the suffix
	 * @param string $appName the name of the app, defaults to the current one
	 */
	public function addStyle($styleName, $appName = null) {
		if ($appName === null) {
			$appName = $this->appName;
		}
		\OCP\Util::addStyle ( $appName, $styleName );
	}

	/**
	 * shorthand for addScript for files in the 3rdparty directory
	 *
	 * @param string $name the name of the file without the suffix
	 */
	public function add3rdPartyScript($name) {
		\OCP\Util::addScript ( $this->appName . '/3rdparty', $name );
	}

	/**
	 * shorthand for addStyle for files in the 3rdparty directory
	 *
	 * @param string $name the name of the file without the suffix
	 */
	public function add3rdPartyStyle($name) {
		\OCP\Util::addStyle ( $this->appName . '/3rdparty', $name );
	}

	/**
	 * Checks if an app is enabled
	 *
	 * @param string $appName the name of an app
	 * @return bool true if app is enabled
	 */
	public function isAppEnabled($appName) {
		return \OCP\App::isEnabled ( $appName );
	}

	/**
	 * used to return and open a new eventsource
	 *
	 * @return \OC_EventSource a new open EventSource class
	 */
	public function openEventSource() {
		// TODO: use public api
		return new \OC_EventSource ();
	}

	/**
	 * @brief connects a function to a hook
	 *
	 * @param string $signalClass class name of emitter
	 * @param string $signalName name of signal
	 * @param string $slotClass class name of slot
	 * @param string $slotName name of slot, in another word, this is the
	 * name of the method that will be called when registered
	 * signal is emitted.
	 * @return bool, always true
	 */
	public function connectHook($signalClass, $signalName, $slotClass, $slotName) {
		return \OCP\Util::connectHook ( $signalClass, $signalName, $slotClass, $slotName );
	}

	/**
	 * @brief Emits a signal.
	 * To get data from the slot use references!
	 *
	 * @param string $signalClass class name of emitter
	 * @param string $signalName name of signal
	 * @param array $params defautl: array() array with additional data
	 * @return bool, true if slots exists or false if not
	 */
	public function emitHook($signalClass, $signalName, $params = array()) {
		return \OCP\Util::emitHook ( $signalClass, $signalName, $params );
	}

	/**
	 * @brief clear hooks
	 *
	 * @param string $signalClass
	 * @param string $signalName
	 */
	public function clearHook($signalClass = false, $signalName = false) {
		if ($signalClass) {
			\OC_Hook::clear ( $signalClass, $signalName );
		}
	}

	/**
	 * Register a backgroundjob task
	 *
	 * @param string $className full namespace and class name of the class
	 * @param string $methodName the name of the static method that should be
	 * called
	 */
	public function addRegularTask($className, $methodName) {
		\OCP\Backgroundjob::addRegularTask ( $className, $methodName );
	}

	/**
	 * Tells ownCloud to include a template in the admin overview
	 *
	 * @param string $mainPath the path to the main php file without the php
	 * suffix, relative to your apps directory! not the template directory
	 * @param string $appName the name of the app, defaults to the current one
	 */
	public function registerAdmin($mainPath, $appName = null) {
		if ($appName === null) {
			$appName = $this->appName;
		}

		\OCP\App::registerAdmin ( $appName, $mainPath );
	}

	/**
	 * Returns the URL for a route
	 *
	 * @param string $routeName the name of the route
	 * @param array $arguments an array with arguments which will be filled into the url
	 * @return string the url
	 */
	public function linkToRoute($routeName, $arguments = array()) {
		return \OCP\Util::linkToRoute ( $routeName, $arguments );
	}

	/**
	 * Returns the link to an image, like link to but only with prepending img/
	 *
	 * @param string $file the name of the file
	 * @param string $appName the name of the app, defaults to the current one
	 */
	public function imagePath($file, $appName = null) {
		if ($appName === null) {
			$appName = $this->appName;
		}
		return \OCP\Util::imagePath ( $appName, $file );
	}

	/**
	 * Returns the translation object
	 *
	 * @return \OC_L10N the translation object
	 */
	public function getTrans() {
		// TODO: use public api
		return \OC_L10N::get ( $this->appName );
	}

	/**
	 * Creates a new navigation entry
	 *
	 * @param array $entry containing: id, name, order, icon and href key
	 */
	public function addNavigationEntry(array $entry) {
		\OCP\App::addNavigationEntry ( $entry );
	}

	/**
	 * Used to abstract the owncloud database access away
	 *
	 * @param string $sql the sql query with ? placeholder for params
	 * @param int $limit the maximum number of rows
	 * @param int $offset from which row we want to start
	 * @return \OCP\DB a query object
	 */
	public function prepareQuery($sql, $limit = null, $offset = null) {
		return \OCP\DB::prepare ( $sql, $limit, $offset );
	}

	/**
	 * Used to get the id of the just inserted element
	 *
	 * @param string $tableName the name of the table where we inserted the item
	 * @return int the id of the inserted element
	 */
	public function getInsertId($tableName) {
		return \OCP\DB::insertid ( $tableName );
	}

	/**
	 *
	 * @return string the name of this app
	 */
	public function getAppName() {
		return $this->appName;
	}

	/**
	 * Writes a function into the error log
	 *
	 * @param string $msg the error message to be logged
	 * @param int $level the error level
	 */
	public function log($msg, $level = null) {
		switch ($level) {
			case 'debug' :
				$level = \OCP\Util::DEBUG;
				break;
			case 'info' :
				$level = \OCP\Util::INFO;
				break;
			case 'warn' :
				$level = \OCP\Util::WARN;
				break;
			case 'fatal' :
				$level = \OCP\Util::FATAL;
				break;
			default :
				$level = \OCP\Util::ERROR;
				break;
		}
		\OCP\Util::writeLog ( $this->appName, $msg, $level );
	}

	/**
	 *
	 * @return Array users in this ownCloud
	 */
	public function getUsers() {
		return \OCP\User::getUsers ();
	}

}
