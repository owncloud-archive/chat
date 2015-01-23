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

class UserOnlineMapperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	public $app;

	/**
	 * @var \OCA\Chat\OCH\Db\UserOnlineMapper
	 */
	public $userOnlineMapper;

	public function setUp(){
		$this->app = new Chat();
		$this->userOnlineMapper  = $this->app->query('UserOnlineMapper');
	}

	public function getOnlineUsersProvider(){
		$user1 = new UserOnline();
		$user1->setUser('foo');
		$user1->setLastOnline(time() -34);
		$user1->setSessionId(md5(time() -32575334));

		$user2 = new UserOnline();
		$user2->setUser('foo');
		$user2->setLastOnline(time() -42352);
		$user2->setSessionId(md5(time() -75698));

		$user3 = new UserOnline();
		$user3->setUser('bar');
		$user3->setLastOnline(time() -346574);
		$user3->setSessionId(md5(time() -4567));

		return array(
			array(
				array(
					$user1,
					$user2,
					$user3
				)
			)
		);
	}
	
	/**
	 * @dataProvider getOnlineUsersProvider
	 */
	public function testGetOnlineUsers($users){
		foreach($users as $user){
			$this->userOnlineMapper->insert($user);
		}

		$result = $this->userOnlineMapper->getOnlineUsers();

		foreach($users as $user){
			$this->assertTrue(in_array($user->getUser(), $result));
		}

	}

	/**
	 * @dataProvider getOnlineUsersProvider
	 */
	public function testGetAll($users){
		$expectedUsers = array();
		foreach($users as $user){
			$r = $this->userOnlineMapper->insert($user);
			$expectedUsers[$r->getId()] = $r;
		}

		$result = $this->userOnlineMapper->getAll();

		foreach($result as $r){
			$expected = $expectedUsers[$r->getId()];
			$this->assertEquals($expected->getUser(), $r->getUser());
			$this->assertEquals($expected->getSessionId(), $r->getSessionId());
			$this->assertEquals($expected->getLastOnline(), $r->getLastOnline());
		}
	}

	/**
	 * @dataProvider getOnlineUsersProvider
	 */
	public function testFindByUser($users){
		$expectedUsers = array();
		foreach($users as $user){
			$r = $this->userOnlineMapper->insert($user);
			$expectedUsers[$r->getId()] = $r;
		}

		$result = $this->userOnlineMapper->findByUser('foo');

		$this->assertEquals(2, count($result));

		foreach($result as $r){
			$expected = $expectedUsers[$r->getId()];
			$this->assertEquals($expected->getUser(), $r->getUser());
			$this->assertEquals($expected->getSessionId(), $r->getSessionId());
			$this->assertEquals($expected->getLastOnline(), $r->getLastOnline());
		}
	}

	/**
	 * @dataProvider getOnlineUsersProvider
	 */
	public function testDeleteBySessionId($users){
		$expectedUsers = array();
		foreach($users as $user){
			$r = $this->userOnlineMapper->insert($user);
			$expectedUsers[$r->getId()] = $r;
		}

		$result = $this->userOnlineMapper->getAll();
		$this->assertEquals(3, count($result));

		$sessionToRemove = $users[1]->getSessionId();
		$this->userOnlineMapper->deleteBySessionId($sessionToRemove);

		$result = $this->userOnlineMapper->getAll();
		$this->assertEquals(2, count($result));

		foreach($result as $r){
			$this->assertNotEquals($sessionToRemove, $r->getSessionId());
		}
	}

	/**
	 * @dataProvider getOnlineUsersProvider
	 */
	public function testUpdateLastOnline($users){
		$this->userOnlineMapper->insert($users[0]);

		$time = time();
		$this->userOnlineMapper->updateLastOnline($users[0]->getSessionId(), $time);
		$result = $this->userOnlineMapper->getAll();

		$this->assertEquals($time, $result[0]->getLastOnline());

	}


	/**
	 * Remove all records from the table so future test can run without problems
	 */
	public function tearDown(){
		$query = \OCP\DB::prepare('DELETE FROM `' . $this->userOnlineMapper->getTableName() . '`');
		$query->execute(array());
	}



}