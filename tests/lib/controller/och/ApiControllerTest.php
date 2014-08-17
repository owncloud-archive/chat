<?php

namespace OCA\Chat\Controller\OCH;

use \OCA\Chat\Utility\ControllerTestUtility;
use \OCA\Chat\App\Chat;
use \OCP\IRequest;

function time(){
	return '2324';
}

class UserMock {

	public function getUID(){
		return 'foo';
	}

}

class ApiControllerTest extends ControllerTestUtility {

	private $appName;
	private $request;
	private $controller;
	private $app;

	public function setUp(){
		$this->appName = 'chat';
		$this->request = $this->getRequest();
		$this->app = $this->getMockBuilder('\OCA\Chat\App\Chat')
			->disableOriginalConstructor()
			->getMock();
		$app = new \OCP\AppFramework\App($this->appName, array());
		$this->app->container = $app->getContainer();
		$this->app->c = $this->app->container;
		$c = $this->app->container;
		$this->app->expects($this->any())
			->method('getContainer')
			->will($this->returnCallback(function() use($c){
				return $c;
			}));
		$this->controller = new ApiController($this->appName, $this->request, $this->app);

		$this->app->c['UserSession'] = $this->getMockBuilder('OC\User\Session')
			->disableOriginalConstructor()
			->getMock();

		$this->app->c['UserSession']->expects($this->any())
			->method('getUser')
			->will($this->returnValue(new UserMock()));

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
		$response = $this->controller->route($type, $data);
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
		$this->app->c[ucfirst($className) . ucfirst($requestType)] = $this->getMockBuilder('\OCA\Chat\OCH\Commands\\' . $className)
				->disableOriginalConstructor()
				->getMock();

		$this->app->c[ucfirst($className) . ucfirst($requestType)]->expects($this->once())
			->method('setRequestData')
			->with($this->equalTo($data))
			->will($this->returnValue(true));

		$this->app->c[ucfirst($className) . ucfirst($requestType)]->expects($this->once())
			->method('execute')
			->will($this->returnValue(null));

		$response = $this->controller->route($type, $data);
		$this->assertEquals('OCA\Chat\OCH\Responses\Success', get_class($response));
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
		$this->app->c[ucfirst($className) . ucfirst($requestType)] = $this->getMockBuilder('\OCA\Chat\OCH\Data\\' . $className)
			->disableOriginalConstructor()
			->getMock();

		$this->app->c[ucfirst($className) . ucfirst($requestType)]->expects($this->once())
			->method('setRequestData')
			->with($this->equalTo($data))
			->will($this->returnValue(true));

		$this->app->c[ucfirst($className) . ucfirst($requestType)]->expects($this->once())
			->method('execute')
			->will($this->returnValue($dummyData));

		$response = $this->controller->route($type, $data);
		$expectedData = array(
			"type" =>  $requestType . '::' . $commandName . '::response',
			"data" => $dummyData
		);
		$expectedData['data']["status"] = "successq";
		$this->assertEquals('OCA\Chat\OCH\Responses\Success', get_class($response));
		$this->assertEquals($expectedData, $response->getData());

	}
}