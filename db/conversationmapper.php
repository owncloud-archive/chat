<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;

class ConversationMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_conversations'); // tablename is news_feeds
    }

}