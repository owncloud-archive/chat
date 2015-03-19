<?php
/**
 * Created by PhpStorm.
 * User: tobis
 * Date: 8/19/14
 * Time: 3:20 PM
 */

namespace OCA\Chat\OCH\Db;

use OCA\Chat\App\Chat;

class PushMessageMapperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	public $app;

	/**
	 * @var \OCA\Chat\OCH\Db\PushMessageMapper
	 */
	public $pushMessageMapper;

	/**
	 * @var \OCA\Chat\OCH\Db\UserOnlineMapper
	 */
	public $userOnlineMapper;

	/**
	 * @var \OCA\Chat\OCH\Db\UserMapper
	 */
	public $userMapper;

	public function setUp(){
		$this->app = new Chat();
		$this->pushMessageMapper = $this->app->query('PushMessageMapper');
		$this->userOnlineMapper = $this->app->query('UserOnlineMapper');
		$this->userMapper = $this->app->query('UserMapper');
	}

	public function pushMessageProvider(){
		$msgs = array();
		$senders = array('bar', 'foobar');
		$sessionId = md5(time() + rand(0, 1000));

		for ($i =0; $i < 20; $i++){
			$msg = new PushMessage();
			$rand = rand(0,1);
			$msg->setSender($senders[$rand]);
			$msg->setReceiver('foo');
			if($i > 10){
				$msg->setReceiverSessionId($sessionId);
			} else {
				$msg->setReceiverSessionId(md5(time() + rand(0, 1000)));
			}
			$msg->setCommand('{"command": "true}');
			$msgs[] = $msg;
		}

		return array(
			array(
				$msgs,
				$sessionId,
			)
		);
	}

	/**
	 * @dataProvider pushMessageProvider
	 */
	public function testFindBysSessionId($msgs, $sessionId){
		foreach($msgs as $msg){
			$this->pushMessageMapper->insert($msg);
		}
		$result = $this->pushMessageMapper->findBysSessionId($sessionId);

		$this->assertEquals(9, count($result));

		foreach($result as $r){
			$this->assertEquals($sessionId, $r->getReceiverSessionId());
		}

	}

	public function sessionsOfAUserProvider(){
		$session1 = new UserOnline();
		$session1->setSessionId(md5(time() + rand(0, 1000)));
		$session1->setUser('foo');
		$session1->setLastOnline(time() + rand(0, 100));

		$session2 = new UserOnline();
		$session2->setSessionId(md5(time() + rand(0, 1000)));
		$session2->setUser('bar');
		$session2->setLastOnline(time() + rand(0, 100));

		$session3 = new UserOnline();
		$session3->setSessionId(md5(time() + rand(0, 1000)));
		$session3->setUser('bar');
		$session3->setLastOnline(time() + rand(0, 100));

		return array(
			array(
				array( $session1, $session2, $session3)
			)
		);
	}

	/**
	 * @dataProvider sessionsOfAUserProvider
	 * @param $users
	 */
	public function testCreateForAllSessionsOfAUser($users){
		foreach($users as $user){
			$this->userOnlineMapper->insert($user);
		}

		$this->pushMessageMapper->createForAllSessionsOfAUser('bar', 'foo', 'testMessage');
		$result = $this->pushMessageMapper->findAll();
		foreach($result as $r){
			$this->assertEquals('foo', $r->getSender());
			$this->assertEquals('bar', $r->getReceiver());
			$this->assertEquals('testMessage', $r->getCommand());
		}
	}

	public function createForAllUsersInConvProvider(){
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
				$convId
			)
		);
	}


	/**
	 * @dataProvider createForAllUsersInConvProvider
	 * @param $users
	 */
	public function testCreateForAllUsersInConv($usersInConv, $sessions, $convId){
		foreach($usersInConv as $user){
			$this->userMapper->insert($user);
		}

		foreach($sessions as $session){
			$this->userOnlineMapper->insert($session);
		}

		$this->pushMessageMapper->createForAllUsersInConv('bar', $convId, 'testCommand');
		$result = $this->pushMessageMapper->findAll();
		$this->assertEquals('bar', $result[0]->getSender());
		$this->assertEquals('foo', $result[0]->getReceiver());
		$this->assertEquals('testCommand', $result[0]->getCommand());
		$this->assertEquals('bar', $result[1]->getSender());
		$this->assertEquals('bar', $result[1]->getReceiver());
		$this->assertEquals('testCommand', $result[1]->getCommand());
	}


	/**
	 * Remove all records from the table so future test can run without problems
	 */
	public function tearDown(){
		$query = \OCP\DB::prepare('DELETE FROM `' . $this->pushMessageMapper->getTableName() . '`');
		$query->execute(array());

		$query = \OCP\DB::prepare('DELETE FROM `' . $this->userOnlineMapper->getTableName() . '`');
		$query->execute(array());

		$query = \OCP\DB::prepare('DELETE FROM `' . $this->userMapper->getTableName() . '`');
		$query->execute(array());
	}
	


}