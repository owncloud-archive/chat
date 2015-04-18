<?php
/**
 * Created by PhpStorm.
 * User: tobis
 * Date: 8/19/14
 * Time: 2:50 PM
 */

namespace OCA\Chat\OCH\Db;

use OCA\Chat\App\Container;


class MessageMapperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \OCA\Chat\App\Container
	 */
	public $container;

	/**
	 * @var \OCA\Chat\OCH\Db\MessageMapper
	 */
	public $messageMapper;

	/**
	 * @var \OCA\Chat\OCH\Db\UserMapper
	 */
	public $userMapper;


	public function setUp(){
		$this->container = new Container();
		$this->messageMapper = $this->container->query('MessageMapper');
		$this->userMapper = $this->container->query('UserMapper');
	}

	public function messageProvider(){
		$msgs = array();
		$users = array('foo', 'bar', 'foobar');
		$convId = md5(time());

		for ($i =0; $i < 20; $i++){
			$msg = new Message();
			$rand = rand(0,2);
			$msg->setUser($users[$rand]);
			$msg->setConvid($convId);
			$msg->setMessage('Test Message');
			$msg->setTimestamp($i);
			$msgs[] = $msg;
		}

		$user = new User();
		$user->setConversationId($convId);
		$user->setUser('foo');
		$user->setJoined(5);
		return array(
			array(
				$msgs,
				$convId,
				$user
			)
		);
	}

	/**
	 * Test if only messages are received send later than we joined AND later than $startpoint = 8
	 * @dataProvider messageProvider
	 * @param $msgs array()
	 * @param $convId string
	 * @param $user \OCA\Chat\OCH\Db\User;
	 */
	public function testGetMessagesByConvIdWitStartPoint($msgs, $convId, $user){
		foreach($msgs as $msg){
			$this->messageMapper->insert($msg);
		}
		$this->userMapper->insert($user);


		$result = $this->messageMapper->getMessagesByConvId($convId, 'foo', 8);

		$this->assertEquals(11, count($result)); // we set startpoint at time "8"

		foreach($result as $r){
			$this->assertGreaterThan(8, $r->getTimestamp());
		}
	}

	/**
	 * Test if only messages are received send later than we joined
	 * @dataProvider messageProvider
	 * @param $msgs array()
	 * @param $convId string
	 * @param $user \OCA\Chat\OCH\Db\User;
	 */
	public function testGetMessagesByConvIdWithoutStartPoint($msgs, $convId, $user){
		foreach($msgs as $msg){
			$this->messageMapper->insert($msg);
		}
		$this->userMapper->insert($user);


		$result = $this->messageMapper->getMessagesByConvId($convId, 'foo');

		$this->assertEquals(14, count($result)); // We joined at time "6", messages start at time "0" and there are 20 messages

		foreach($result as $r){
			$this->assertGreaterThan(5, $r->getTimestamp());
		}
	}


	/**
	 * Remove all records from the table so future test can run without problems
	 */
	public function tearDown(){
		$query = \OCP\DB::prepare('DELETE FROM `' . $this->messageMapper->getTableName() . '`');
		$query->execute(array());

		$query = \OCP\DB::prepare('DELETE FROM `' . $this->userMapper->getTableName() . '`');
		$query->execute(array());
	}
	


}