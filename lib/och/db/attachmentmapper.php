<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\OCH\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCA\Chat\OCH\Commands\AttachFile;
use \OCP\AppFramework\Db\Mapper;
use \OCP\IDb;

class AttachmentMapper extends Mapper {

	public function __construct(IDb $api) {
		parent::__construct($api, 'chat_attachments');
	}

	public function insertUnique(Attachment $entity){
		try {
			$sql = <<<SQL
				SELECT
					*
				FROM
					`*PREFIX*chat_attachments`
				WHERE
					`owner` = ?
				AND
					`file_id` = ?
				AND
					`conv_id` = ?
SQL;
			$this->findEntity($sql, array($entity->getOwner(), $entity->getFileId(), $entity->getConvId()));
		} catch (DoesNotExistException $e){
			$this->insert($entity);
		}
	}

}