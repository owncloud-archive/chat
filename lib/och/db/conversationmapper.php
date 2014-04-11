<?php
namespace OCA\Chat\OCH\Db;

use \OCA\Chat\Db\Mapper;
use \OCA\Chat\Core\API;
use OCA\Chat\Db\DoesNotExistException;

class ConversationMapper extends Mapper {

    public function __construct(API $api) {
	\OCP\Util::writeLog('chat', 'conv mapper : ' . $api->getUserId(), \OCP\Util::ERROR);
	parent::__construct($api, 'chat_och_conversations'); // tablename is news_feeds
    }

    public function deleteConversation($conversationID){
        $sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE `conversation_id` = ? ';
        $this->execute($sql, array($conversationID));
    }

    public function findByConversationId($conversationID){
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` ' . 'WHERE `conversation_id` = ?';
        return $this->findEntity($sql, array($conversationID));
    }

    public function exists($id){
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` ' . 'WHERE `conversation_id` = ?';
        try{
            $this->findEntity($sql, array($id));
            return ture;
        } catch (DoesNotExistException $exception) {
            return false;
        }
    }	
}