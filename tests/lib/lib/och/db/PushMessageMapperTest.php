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
		$this->pushMessageMapper = $this->app->c['PushMessageMapper'];
		$this->userOnlineMapper = $this->app->c['UserOnlineMapper'];
		$this->userMapper = $this->app->c['UserMapper'];
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