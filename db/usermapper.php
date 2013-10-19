<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;

class UserMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_users_in_conversation'); // tablename is news_feeds
    }

}