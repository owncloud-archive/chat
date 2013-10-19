<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;

class PushMessageMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_push_messages'); // tablename is news_feeds
    }

}