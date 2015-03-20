<?php

namespace OCA\Chat\Controller\OCH;

use \OCA\Chat\Utility\ControllerTestUtility;
use \OCA\Chat\App\Chat;
use \OCA\Chat\App\Container;
use \OCP\IRequest;
use \OCA\Chat\Controller\OCH\ApiController;


function time(){
	return '2324';
}

/**
 * Class UserMock
 * This class mocks \OC\User\User
 */
class UserMock {

	public function getUID(){
		return 'foo';
	}

}

class ApiControllerTest extends ControllerTestUtility {

	/**
	 * @var string
	 */
	private $appName;

	/**
	 * @var \OCP\Irequest
	 */
	private $request;

	/**
	 * @var \OCA\Chat\Controller\OCH\ApiController
	 */
	private $controller;

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	private $app;

	public function setUp(){
		$this->appName = 'chat';
		$this->request =  $this->getMockBuilder('\OCP\IRequest')
			->disableOriginalConstructor()
			->getMock();

		$this->container = $this->getMockBuilder('\OCA\Chat\App\Container')
			->disableOriginalConstructor()
			->getMock();

		$this->chat = $this->getMockBuilder('\OCA\Chat\App\Chat')
			->disableOriginalConstructor()
			->getMock();

		$this->chatAPI = $this->getMockBuilder('\OCA\Chat\OCH\ChatAPI')
			->disableOriginalConstructor()
			->getMock();

		$this->controller = new ApiController(
			$this->appName,
			$this->request,
			$this->chat,
			$this->container
		);
	}

	public function testRouteAnnotations(){
		$expectedAnnotations = array('NoAdminRequired');
		$this->assertAnnotations($this->controller, 'route', $expectedAnnotations);
	}

	public function routeProvider(){
		return array(
			// test invalid HTTP type
			array(
				"command::join::response",
				array(),
				array(
					"type" => "command::join::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::INVALID_HTTP_TYPE
					)
				)
			),
			// test ommitted session id
			array(
				"command::blub::request",
				array(),
				array(
					"type" => "command::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::NO_SESSION_ID
					)
				)
			),
			// test ommited user
			array(
				"command::blub::request",
				array(
					"session_id" => md5(time())
				),
				array(
					"type" => "command::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::NO_USER
					)
				)
			),
			// test invalid http type with data as requesttype
			array(
				"data::join::response",
				array(),
				array(
					"type" => "data::join::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::INVALID_HTTP_TYPE
					)
				)
			),
			// test ommitted session id with data as requesttype
			array(
				"data::blub::request",
				array(),
				array(
					"type" => "data::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::NO_SESSION_ID
					)
				)
			),
			// test ommitted user with data as requesttype
			array(
				"data::blub::request",
				array(
					"session_id" => md5(time())
				),
				array(
					"type" => "data::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::NO_USER
					)
				)
			),
			// test invalid http type with push as requesttype
			array(
				"push::join::response",
				array(),
				array(
					"type" => "push::join::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::INVALID_HTTP_TYPE
					)
				)
			),
			// test ommitted session id with push as requesttype
			array(
				"push::blub::request",
				array(),
				array(
					"type" => "push::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::NO_SESSION_ID
					)
				)
			),
			// test ommitted user with push as requesttype
			array(
				"push::blub::request",
				array(
					"session_id" => md5(time())
				),
				array(
					"type" => "push::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::NO_USER
					)
				)
			),
			// test not existent command
			array(
				"command::blub::request",
				array(
					"session_id" => md5(time()),
					"user" => array(
						'id' => 'foo',
						'displayname' => 'foo',
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
				),
				array(
					"type" => "command::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::COMMAND_NOT_FOUND
					)
				)
			),
			// test not existent data request
			array(
				"data::blub::request",
				array(
					"session_id" => md5(time()),
					"user" => array(
						'id' => 'foo',
						'displayname' => 'foo',
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
				),
				array(
					"type" => "data::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::DATA_ACTION_NOT_FOUND
					)
				)
			),
			// test not existent push request
			array(
				"push::blub::request",
				array(
					"session_id" => md5(time()),
					"user" => array(
						'id' => 'foo',
						'displayname' => 'foo',
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
				),
				array(
					"type" => "push::blub::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::PUSH_ACTION_NOT_FOUND
					)
				)
			),
			// test user not equal to the logged in OC user
			array(
				"push::get::request",
				array(
					"session_id" => md5(time()),
					"user" => array(
						'id' => 'bar',
						'displayname' => 'bar',
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'bar',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
				),
				array(
					"type" => "push::get::response",
					"data" => array(
						"status" => "error",
						"msg" => ApiController::USER_NOT_EQUAL_TO_OC_USER
					)
				)
			),
		);
	}

	/**
	 * Test if wrong request data will result in the correct error
	 * @dataProvider routeProvider
	 */
	public function	testError($type, $data, $expectedData){
		$this->chat->expects($this->any())
			->method('getUserId')
			->will($this->returnValue('foo'));

		$this->container->expects($this->any())
			->method('query')
			->will($this->returnValue($this->chatAPI));

		$response = $this->controller->route($type, $data);
		$this->assertInstanceOf('\OCA\Chat\OCH\Responses\Error', $response);
		$this->assertEquals($expectedData, $response->getData());
	}

	public function commandExecutionProivder(){
		return array(
			array(
				"command::join::request",
				array(
					"conv_id" => md5(time() + 2343),
					"timestamp" => time(),
					"user" => array(
						'id' => 'foo',
						'displayname' => 'foo',
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
					"session_id" => md5(time() -324234)
				),
				'join',
				'command'
			),
		);
	}

	/**
	 * Test if the correct class is executed when asked
	 * @dataProvider commandExecutionProivder
	 */
	public function testCommandExecution($type, $data, $className, $requestType){
		$this->chat->expects($this->any())
			->method('getUserId')
			->will($this->returnValue('foo'));

		$this->container->expects($this->any())
			->method('query')
			->will($this->returnCallback(function() use($className, $requestType, $data){
				$class = $this->getMockBuilder('\OCA\Chat\OCH\ChatAPI')
					->disableOriginalConstructor()
					->getMock();

				$class->expects($this->once())
					->method('setRequestData')
					->with($this->equalTo($data))
					->will($this->returnValue(true));

				$class->expects($this->once())
					->method('execute')
					->will($this->returnValue(null));

				return $class;
			}));


		$response = $this->controller->route($type, $data);
		$this->assertInstanceOf('\OCA\Chat\OCH\Responses\Success', $response);
		$this->assertEquals(array("type" =>  $requestType . '::' . $className . '::response', "data" => array("status" => "success")), $response->getData());

	}

	public function dataExecutionProvider(){
		return array(
			array(
				"data::get_users::request",
				array(
					"conv_id" => md5(time() + 2343),
					"timestamp" => time(),
					"user" => array(
						'id' => 'foo',
						'displayname' => 'foo',
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
					"session_id" => md5(time() -324234)
				),
				'GetUsers',
				'data',
				array('dummy' => 'dummydata'),
				'get_users'
			),
		);
	}

	/**
	 * @dataProvider dataExecutionProvider
	 */
	public function testDataExecution($type, $data, $className, $requestType, $dummyData, $commandName){
		$this->chat->expects($this->any())
			->method('getUserId')
			->will($this->returnValue('foo'));

		$this->container->expects($this->any())
			->method('query')
			->will($this->returnCallback(function() use($className, $requestType, $data, $dummyData){
				$class = $this->getMockBuilder('\OCA\Chat\OCH\ChatAPI')
					->disableOriginalConstructor()
					->getMock();

				$class->expects($this->once())
					->method('setRequestData')
					->with($this->equalTo($data))
					->will($this->returnValue(true));

				$class->expects($this->once())
					->method('execute')
					->will($this->returnValue($dummyData));

				return $class;
			}));

		$response = $this->controller->route($type, $data);
		$expectedData = array(
			"type" =>  $requestType . '::' . $commandName . '::response',
			"data" => $dummyData
		);
		$expectedData['data']["status"] = "success";
		$this->assertInstanceOf('\OCA\Chat\OCH\Responses\Success', $response);
		$this->assertEquals($expectedData, $response->getData());

	}

	public function pushExecutionProvider(){
		return array(
			array(
				"push::get::request",
				array(
					"conv_id" => md5(time() + 2343),
					"timestamp" => time(),
					"user" => array(
						'id' => 'foo',
						'displayname' => 'foo',
						'backends' => array (
							'email' => array (
								'id' => NULL,
								'displayname' => 'E-mail',
								'protocol' => 'email',
								'namespace' => ' email',
								'value' => array (
									0 => array (
									),
								),
							),
							'och' => array (
								'id' => NULL,
								'displayname' => 'ownCloud Handle',
								'protocol' => 'x-owncloud-handle',
								'namespace' => 'och',
								'value' => 'foo',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
					"session_id" => md5(time() -324234)
				),
				'get',
				'push',
				array('dummy' => 'dummydata'),
			),
		);
	}

	/**
	 * @dataProvider pushExecutionProvider
	 */
	public function testPushExecution($type, $data, $className, $requestType, $dummyData){
		$this->chat->expects($this->any())
			->method('getUserId')
			->will($this->returnValue('foo'));

		$this->container->expects($this->any())
			->method('query')
			->will($this->returnCallback(function() use($className, $requestType, $data, $dummyData){
				$class = $this->getMockBuilder('\OCA\Chat\OCH\ChatAPI')
					->disableOriginalConstructor()
					->getMock();

				$class->expects($this->once())
					->method('setRequestData')
					->with($this->equalTo($data))
					->will($this->returnValue(true));

				$class->expects($this->once())
					->method('execute')
					->will($this->returnValue($dummyData));

				return $class;
			}));

		$response = $this->controller->route($type, $data);
		$expectedData = array(
			'dummy' => 'dummydata'
		);
		$this->assertEquals($expectedData, $response);
	}

}