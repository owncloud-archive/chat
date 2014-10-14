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

	public function __construct(IDb $api, $app) {
		parent::__construct($api, 'chat_attachments');
		$this->app = $app;
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

	public function findRawByConv($convId){
		$sql = <<<SQL
				SELECT
					*
				FROM
					`*PREFIX*chat_attachments`
				WHERE
					`conv_id` = ?
SQL;
		$files = array();
		$result = $this->findEntities($sql, array($convId));
		foreach ($result as $r) {
			$files[] = array(
				"path" => $r->getPath(),
				"user" => $this->app->getUserasContact($r->getOwner()),
				"timestamp" => $r->getTimestamp(),
			);
		}

		return $files;
	}

	public function deleteByConvAndFileID(Attachment $file){
		$sql = <<<SQL
				DELETE
				FROM
					`*PREFIX*chat_attachments`
				WHERE
					`conv_id` = ?
				AND
					`file_id`= ?
SQL;

		$this->execute($sql, array($file->getConvId(), $file->getFileId()));
	}

    public function findByPathAndConvId($path, $convId){
        $sql = <<<SQL
				SELECT
                    *
				FROM
                    `*PREFIX*chat_attachments`
				WHERE
					`conv_id` = ?
				AND
					`path`= ?
SQL;

        return $this->findEntity($sql, array($convId, $path));
    }

}