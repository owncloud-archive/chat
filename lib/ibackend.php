<?php
/**
 * Copyright (c) 2014 Tobia De Koninck <hey--at--ledfan.be>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Chat;

interface IBackend{

	/**
	 * @return string identifier of the backend. Should be unique.
	 */
	public function getId();

	/**
	 * This function is used to get the conversations which should be shown when the Chat app is loaded.
	 * @return array
	 */
	public function getInitConvs();

	/**
	 * @return string displayname of this backend
	 */
	public function getDisplayName();

	/**
	 * @return supported protocols by this backend
	 */
	public function getProtocols();

	/**
	 * Check whether this backend supports $protocol
	 * @param $protocol
	 * @return nulll
	 */
	public function hasProtocol($protocol);

	/**
	 * @return bool
	 */
	public function isEnabled();

	/**
	 * Config options for this Backend
	 * @return array
	 */
	public function getConfig();

	/**
	 * Converts this object to an array to be encoded as JSON
	 * @return array
	 */
	public function toArray();
}