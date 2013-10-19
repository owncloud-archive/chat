<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;

class UserOnlineMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_users_online'); // tablename is news_feeds
    }

}