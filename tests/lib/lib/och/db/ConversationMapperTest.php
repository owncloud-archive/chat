<?php
/**
 * Created by PhpStorm.
 * User: tobis
 * Date: 8/19/14
 * Time: 1:50 PM
 */

namespace OCA\Chat\OCH\Db;

use \OCA\Chat\App\Chat;
use \OCA\Chat\OCH\Db\User;
use \OCA\Chat\OCH\Db\UserMapper;

class ConversationMapperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	public $app;

	/**
	 * @var \OCA\Chat\OCH\Db\ConversationMapper
	 */
	public $conversationMapper;

	/**
	 * @var \OCA\Chat\OCH\Db\UserMapper
	 */
	public $userMapper;



	public function setUp(){
		$this->app = new Chat();
		$this->conversationMapper = $this->app->c['ConversationMapper'];
		$this->userMapper = $this->app->c['UserMapper'];
	}

	public function convProvider(){
		$conv1 = new Conversation();
		$conv1->setConversationId(md5(time()));

		$conv2 = new Conversation();
		$conv2->setConversationId(md5(time() - 3434));


		$conv3 = new Conversation();
		$conv3->setConversationId(md5(time() - 3432452345334));


		$conv4 = new Conversation();
		$conv4->setConversationId(md5(time() - 23455854634));

		return array(
			array(
				$conv1,
				$conv2,
				$conv3,
				$conv4
			)
		);
	}

	/**
	 * @dataProvider convProvider
	 * @param $conv1 \OCA\Chat\OCH\Db\Conversation;
	 * @param $conv2 \OCA\Chat\OCH\Db\Conversation
	 */
	public function testFindByConversationId($conv1, $conv2){
		$this->conversationMapper->insert($conv1);
		$this->conversationMapper->insert($conv2);

		$result1 = $this->conversationMapper->findByConversationId($conv1->getConversationId());
		$result2 = $this->conversationMapper->findByConversationId($conv2->getConversationId());

		$this->assertEquals(
			$conv1->getConversationId(),
			$result1->getConversationId()
		);
		$this->assertEquals(
			$conv1->getId(),
			$result1->getId()
		);
		$this->assertEquals(
			$conv2->getConversationId(),
			$result2->getConversationId()
		);
		$this->assertEquals(
			$conv2->getId(),
			$result2->getId()
		);
	}
	/**
	 * This also tests existsByConvId
	 * @dataProvider convProvider
	 * @param $conv1 \OCA\Chat\OCH\Db\Conversation
	 * @param $conv2 \OCA\Chat\OCH\Db\Conversation
	 * @param $conv3 \OCA\Chat\OCH\Db\Conversation
	 * @param $conv4 \OCA\Chat\OCH\Db\Conversation
	 */
	public function testDelete($conv1, $conv2, $conv3, $conv4){
		$this->conversationMapper->insert($conv1);
		$this->conversationMapper->insert($conv2);
		$this->conversationMapper->insert($conv3);
		$this->conversationMapper->insert($conv4);

		// Delete 2 conversation
		$this->conversationMapper->deleteConversation($conv1->getConversationId());
		$this->conversationMapper->deleteConversation($conv4->getConversationId());

		$this->assertEquals(
			false,
			$this->conversationMapper->existsByConvId($conv1->getConversationId())
		);
		$this->assertEquals(
			false,
			$this->conversationMapper->existsByConvId($conv4->getConversationId())
		);
		$this->assertEquals(
			true,
			$this->conversationMapper->existsByConvId($conv2->getConversationId())
		);
		$this->assertEquals(
			true,
			$this->conversationMapper->existsByConvId($conv3->getConversationId())
		);
	}

	public function existsByUserProvider(){
		$conv1Id = md5(time());
		$conv1 = new Conversation();
		$conv1->setConversationId($conv1Id);

		$user1InConv1 = new User();
		$user1InConv1->setConversationId($conv1Id);
		$user1InConv1->setUser('foo');
		$user1InConv1->setJoined(time() - 53353);

		$user2InConv1 = new User();
		$user2InConv1->setConversationId($conv1Id);
		$user2InConv1->setUser('bar');
		$user2InConv1->setJoined(time() - 53353);

		$conv2Id = md5(time() - 3434);
		$conv2 = new Conversation();
		$conv2->setConversationId($conv2Id);

		$user1InConv2 = new User();
		$user1InConv2->setConversationId($conv2Id);
		$user1InConv2->setUser('foo');
		$user1InConv2->setJoined(time() - 53353);

		$user2InConv2 = new User();
		$user2InConv2->setConversationId($conv2Id);
		$user2InConv2->setUser('bar');
		$user2InConv2->setJoined(time() - 53353);

		$user3InConv2 = new User();
		$user3InConv2->setConversationId($conv2Id);
		$user3InConv2->setUser('foobar');
		$user3InConv2->setJoined(time() - 53353);

		return array(
			array(
				$conv1,
				$user1InConv1,
				$user2InConv1,
				$conv2,
				$user1InConv2,
				$user2InConv2,
				$user3InConv2
			)
		);
	}

	/**
	 * @dataProvider existsByUserProvider
	 * @param $conv1 \OCA\Chat\OCH\Db\Conversation
	 * @param $user1InConv1 \OCA\Chat\OCH\Db\User
	 * @param $user2InConv1 \OCA\Chat\OCH\Db\User
	 * @param $conv2 \OCA\Chat\OCH\Db\Conversation
	 * @param $user1InConv2 \OCA\Chat\OCH\Db\User
	 * @param $user2InConv2 \OCA\Chat\OCH\Db\User
	 * @param $user3InConv2 \OCA\Chat\OCH\Db\User
	 */
	public function testExistsByUsers(
		$conv1,
		$user1InConv1,
		$user2InConv1,
		$conv2,
		$user1InConv2,
		$user2InConv2,
		$user3InConv2
	){
		// Insert dummy data
		$this->conversationMapper->insert($conv1);
		$this->userMapper->insert($user1InConv1);
		$this->userMapper->insert($user2InConv1);
		$this->conversationMapper->insert($conv2);
		$this->userMapper->insert($user1InConv2);
		$this->userMapper->insert($user2InConv2);
		$this->userMapper->insert($user3InConv2);

		// Test if we can find $conv1, by providing $user1Inconv1 and $user2InConv1
		$result1 = $this->conversationMapper->existsByUsers(array(
			$user1InConv1->getUser(),
			$user2InConv1->getUser()
		));

		$this->assertEquals($conv1->getConversationId(), $result1['conv_id']);

		// Test if we can find $conv2, by providing $user1Inconv2 and $user2InConv2 $user3InConv2
		$result2 = $this->conversationMapper->existsByUsers(array(
			$user1InConv2->getUser(),
			$user2InConv2->getUser(),
			$user3InConv2->getUser(),
		));

		$this->assertEquals($conv2->getConversationId(), $result2['conv_id']);


	}



	/**
	 * Remove all records from the table so future test can run without problems
	 */
	public function tearDown(){
		$query = \OCP\DB::prepare('DELETE FROM `' . $this->conversationMapper->getTableName() . '`');
		$query->execute(array());

		$query = \OCP\DB::prepare('DELETE FROM `' . $this->userMapper->getTableName() . '`');
		$query->execute(array());
 	}



}