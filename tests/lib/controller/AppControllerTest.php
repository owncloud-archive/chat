<?php

namespace OCA\Chat\Controller;

use \OCA\Chat\Utility\ControllerTestUtility;
use \OCA\Chat\App\Chat;
use \OCP\IRequest;

function time(){
	return '2324';
}

class AppControllerTest extends ControllerTestUtility {

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
		$this->app->expects($this->any())
			->method('getContainer')
			->will($this->returnCallback(function(){
				return $this->app->container;
			}));
		$this->controller = new AppController($this->appName, $this->request, $this->app);
	}


	public function testIndexAnnotations(){
		$expectedAnnotations = array('NoAdminRequired', 'NoCSRFRequired');
		$this->assertAnnotations($this->controller, 'index', $expectedAnnotations);
	}

	public function testContactsAnnotations(){
		$expectedAnnotations = array('NoAdminRequired');
		$this->assertAnnotations($this->controller, 'index', $expectedAnnotations);
	}


	public function indexProvider(){
		return array(
			array(
				array(
					'id' => 'admin',
					'displayname' => 'admin',
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
							'value' => 'admin',
						),
					),
					'address_book_id' => 'local',
					'address_book_backend' => '',
				),
				array(
					"timestamp" => time(),
					"user" => array(
						'id' => 'admin',
						'displayname' => 'admin',
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
								'value' => 'admin',
							),
						),
						'address_book_id' => 'local',
						'address_book_backend' => '',
					),
				),
				array(
					"contacts" => "contacts", // dummy data
					"contactsList" => array("contact1", "contact2"),
					"contactsObj" => array("contacts"),
				),
				array(
					"backend1",
					"backend2"
				),
				array(
					"initconv1",
					"initconv2"
				),
				array(
					"session_id" => md5(time())
				)
			)
		);
	}

	/**
	 * @dataProvider indexProvider
	 */
	public function testIndex($currentUser, $greetRequestData, $contacts, $backends, $initConvs, $sessionId){
		$this->app->expects($this->once())
			->method('getCurrentUser')
			->will($this->returnValue($currentUser));

		$this->app->expects($this->once())
			->method('getContacts')
			->will($this->returnValue($contacts));

		$this->app->expects($this->once())
			->method('getBackends')
			->will($this->returnValue($backends));

		$this->app->expects($this->once())
			->method('getInitConvs')
			->will($this->returnValue($initConvs));


		$this->app->c['GreetCommand'] = $this->getMockBuilder('OCA\Chat\OCH\Commands\Greet')
			->disableOriginalConstructor()
			->getMock();

		$this->app->c['GreetCommand']->expects($this->once())
			->method('setRequestData')
			->with($greetRequestData)
			->will($this->returnValue(true));

		$this->app->c['GreetCommand']->expects($this->once())
			->method('execute')
			->will($this->returnValue($sessionId));


		$expectedParams = array(
			"initvar" => json_encode(array(
				"contacts" => $contacts['contacts'],
				"contactsList" => $contacts['contactsList'],
				"contactsObj" => $contacts['contactsObj'],
				"backends" => $backends,
				"initConvs" => $initConvs,
				"sessionId" => $sessionId['session_id'],
			))
		);

		$response = $this->controller->index();
		$this->assertEquals('main', $response->getTemplateName());
		$this->assertEquals($expectedParams, $response->getParams());
	}


	public function contactsProvider(){
		return array(
			array(
				array(
					"contacts" => "contacts", // dummy data
					"contactsList" => array("contact1", "contact2"),
					"contactsObj" => array("contacts"),
				),
			)
		);
	}

	/**
	 * @dataProvider contactsProvider
	 */
	public function testContacts($contacts){
		$this->app->expects($this->once())
			->method('getContacts')
			->will($this->returnValue($contacts));

		$expectedData = array(
			"contacts" => $contacts['contacts'],
			"contactsList" => $contacts['contactsList'],
			"contactsObj" => $contacts['contactsObj']
		);

		$response = $this->controller->contacts();
		$this->assertEquals('OCP\AppFramework\Http\JSONResponse', get_class($response)); // make sure a JSON response is sent
 		$this->assertEquals($expectedData, $response->getData());
	}

}