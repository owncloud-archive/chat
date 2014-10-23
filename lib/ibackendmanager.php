<?php
/**
 * Copyright (c) 2014 Tobia De Koninck <hey--at--ledfan.be>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Chat;

interface IBackendManager{

	/**
	 * @param IBackend $backend
	 * @return null
	 */
	public static function registerBackend(IBackend $backend);

	/**
	 * @param IBackend $backend
	 * @return null
	 */
	public static function unRegisterBackend(IBackend $backend);

	/**
	 * Returns the enabled backends
	 * @return array with enabled backends IBackend
	 */
	public function getEnabledBackends();

	/**
	 * @param $id
	 * @return IBackend
	 */
	public function getBackend($id);

	/**
	 * @param $protocol one of the protocols stored in \OCP\Chat\IBackend::protocols
	 * @return IBackend
	 */
	public function getBackendByProtocol($protocol);

}
