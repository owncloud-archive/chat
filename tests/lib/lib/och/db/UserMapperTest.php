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

class UserMapperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	public $app;

	/**
	 * @var \OCA\Chat\OCH\Db\UserMapper
	 */
	public $userMapper;

	/**
	 * @var \OCA\Chat\OCH\Db\UserOnlineMapper
	 */
	public $userOnlineMapper;



	public function setUp(){
		$this->app = new Chat();
		$this->userMapper = $this->app->query('UserMapper');
		$this->userOnlineMapper  = $this->app->query('UserOnlineMapper');
	}

	public function findSessionsByConversationProvider(){
		$convId = md5(time() - 354235);
		$sessionId1 = md5(time() + rand(0, 1000));
		$sessionId2 = md5(time() + rand(0, 1000));
		$user1 = new User();
		$user1->setUser('foo');
		$user1->setConversationId($convId);
		$user1->setJoined(time() -3434);

		$user2 = new User();
		$user2->setUser('bar');
		$user2->setConversationId($convId);
		$user2->setJoined(time() -4345675);

		$session1 = new UserOnline();
		$session1->setSessionId($sessionId1);
		$session1->setUser('foo');
		$session1->setLastOnline(time() -400);

		$session2 = new UserOnline();
		$session2->setSessionId($sessionId2);
		$session2->setUser('bar');
		$session2->setLastOnline(time() -3435);


		return array(
			array(
				array($user1, $user2),
				array($session1, $session2),
				$convId,
				$sessionId1,
				$sessionId2
			)
		);
	}

	/**
	 * @dataProvider findSessionsByConversationProvider
	 */
	public function testFindSessionsByConversation($users, $sessions, $convId, $sessionId1, $sessionId2){
		foreach($users as $user){
			$this->userMapper->insert($user);
//			$result->setSessionId($sessionIds[$user->getUser()]);
		}

		foreach($sessions as $session){
			$this->userOnlineMapper->insert($session);
		}

		$result = $this->userMapper->findSessionsByConversation($convId);

		$sessions = array();
		$users = array();
		foreach($result as $r){
			$sessions[] = $r->getSessionId();
			$users[] = $r->getUser();
//			$this->assertEquals($expected->getUser(), $r->getUser());
//			$this->assertEquals($expected->getSessionId(), $r->getSessionId());
//			$this->assertEquals($expected->getJoined(), $r->getJoined());
//			$this->assertEquals($expected->getConversationId(), $r->getConversationId());
		}
		$this->assertTrue(in_array('foo', $users));
		$this->assertTrue(in_array('bar', $users));
		$this->assertTrue(in_array($sessionId1, $sessions));
		$this->assertTrue(in_array($sessionId2, $sessions));


	}

	/**
	 * @return array
	 */
	public function findByUserProvider(){
		$user1 = new User();
		$user1->setConversationId(md5(time() + 32324));
		$user1->setJoined(time() -34434);
		$user1->setUser('foo');

		$user2 = new User();
		$user2->setConversationId(md5(time() + 3890));
		$user2->setJoined(time() - 35434);
		$user2->setUser('foo');

		$user3 = new User();
		$user3->setConversationId(md5(time() + 53890));
		$user3->setJoined(time() - 35434434);
		$user3->setUser('bar');

		return array(
			array(
				$user1,
				$user2,
				$user3
			)
		);
	}

	/**
	 * @param User $user1
	 * @param User $user2
	 * @param User $user3
	 * @dataProvider findByUserProvider
	 */
	public function testFindByUser(User $user1, User $user2, User $user3){
		$this->userMapper->insert($user1);
		$this->userMapper->insert($user2);
		$this->userMapper->insert($user3);


		$result = $this->userMapper->findByUser('foo');

		$this->assertEquals(2, count($result));
		$this->assertEquals($result[0]->getUser(), 'foo');
		$this->assertEquals($result[1]->getUser(), 'foo');
	}

	/**
	 * @param User $user1
	 * @param User $user2
	 * @param User $user3
	 * @dataProvider findByUserProvider
	 */
	public function testFindConvsIdByUser(User $user1, User $user2, User $user3){
		$this->userMapper->insert($user1);
		$this->userMapper->insert($user2);
		$this->userMapper->insert($user3);


		$result = $this->userMapper->findConvsIdByUser('foo');

		$this->assertEquals(2, count($result));
		$this->assertTrue(in_array($user1->getConversationId(), $result));
		$this->assertTrue(in_array($user2->getConversationId(), $result));
	}

	public function findUsersInConvProvider(){
		$id = md5(time() + 34234);
		$user1 = new User();
		$user1->setConversationId($id);
		$user1->setJoined(time() -34434);
		$user1->setUser('foo');

		$user2 = new User();
		$user2->setConversationId($id);
		$user2->setJoined(time() - 35434);
		$user2->setUser('foo');

		$user3 = new User();
		$user3->setConversationId(md5(time() -3));
		$user3->setJoined(time() - 35434);
		$user3->setUser('bar');

		return array(
			array(
				$user1,
				$user2,
				$user3,
				$id
			)
		);
	}
	
	/**
	 * @param User $user1
	 * @param User $user2
	 * @param User $user3
	 * @dataProvider findUsersInConvProvider
	 */
	public function testFindUsersInConv(User $user1, User $user2, User $user3, $id){
		$this->userMapper->insert($user1);
		$this->userMapper->insert($user2);
		$this->userMapper->insert($user3);

		$result = $this->userMapper->findUsersInConv($id);

		$this->assertEquals(1, count($result));
		$this->assertTrue(in_array($user1->getUser(), $result));
		$this->assertTrue(in_array($user2->getUser(), $result));

	}

	public function insertUniqueProvider(){
		$user = new User();
		$user->setUser('foo');
		$user->setConversationId(md5(time() -2342));
		$user->setJoined(234234234);
		return array(
			array(
				$user
			)
		);
	}

	/**
	 * @param User $user
	 * @dataProvider insertUniqueProvider
	 */
	public function testInsertUnique(User $user){
		$this->userMapper->insertUnique($user);
		$this->userMapper->insertUnique($user);
		$this->userMapper->insertUnique($user);

		$result = $this->userMapper->findAll();

		$this->assertEquals(1, count($result));
		$this->assertEquals($user->getUser(), $result[0]->getUser());
		$this->assertEquals($user->getConversationId(), $result[0]->getConversationId());
		$this->assertEquals($user->getJoined(), $result[0]->getJoined());



	}
	
	/**
	 * Remove all records from the table so future test can run without problems
	 */
	public function tearDown(){
		$query = \OCP\DB::prepare('DELETE FROM `' . $this->userMapper->getTableName() . '`');
		$query->execute(array());

		$query = \OCP\DB::prepare('DELETE FROM `' . $this->userOnlineMapper->getTableName() . '`');
		$query->execute(array());
	}



}