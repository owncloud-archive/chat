<?php

namespace OCA\Chat\Controller;


use \OCA\Chat\Utility\ControllerTestUtility;
use \OCA\Chat\App\Chat;
use \OCP\IRequest;
use OCA\Chat\OCH\OCH;


function time(){
	return '2324';
}

class AppControllerTest extends ControllerTestUtility {

	/**
	 * @var string
	 */
	private $appName;

	/**
	 * @var \OCP\Irequest
	 */
	private $request;

	/**
	 * @var \OCA\Chat\Controller\AppController
	 */
	private $controller;

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	private $chat;

	public function setUp(){
		$this->appName = 'chat';
		$this->request =  $this->getMockBuilder('\OCP\IRequest')
			->disableOriginalConstructor()
			->getMock();

		$this->userOnlineMapper = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();

		$this->och = $this->getMockBuilder('\OCA\Chat\OCH\OCH')
			->disableOriginalConstructor()
			->getMock();

		$this->syncOnline = $this->getMockBuilder('\OCA\Chat\OCH\Commands\SyncOnline')
			->disableOriginalConstructor()
			->getMock();

		$this->contactsManager = $this->getMockBuilder('\OCP\Contacts\IManager')
			->disableOriginalConstructor()
			->getMock();

		$this->backendManager = $this->getMockBuilder('\OCA\Chat\IBackendManager')
			->disableOriginalConstructor()
			->getMock();

		$this->user = $this->getMockBuilder('\OCP\IUser')
			->disableOriginalConstructor()
			->getMock();

		$this->rootFolder = $this->getMockBuilder('\OCP\Files\IRootFolder')
			->disableOriginalConstructor()
			->getMock();

		$this->greet = $this->getMockBuilder('\OCA\Chat\OCH\Commands\Greet')
			->disableOriginalConstructor()
			->getMock();

		$this->greet = $this->getMockBuilder('\OCP\IConfig')
			->disableOriginalConstructor()
			->getMock();

		$this->chat =  new Chat(
			$this->backendManager,
			$this->userOnlineMapper,
			$this->syncOnline,
			$this->user,
			$this->contactsManager,
			$this->rootFolder
		);

		$this->controller = new AppController(
			$this->appName,
			$this->request,
			$this->chat,
			$this->contactsManager,
			$this->config,
			$this->greet
		);
	}


	public function testIndexAnnotations(){
		$expectedAnnotations = array('NoAdminRequired', 'NoCSRFRequired');
		$this->assertAnnotations($this->controller, 'index', $expectedAnnotations);
	}

	public function testContactsAnnotations(){
		$expectedAnnotations = array('NoAdminRequired');
		$this->assertAnnotations($this->controller, 'index', $expectedAnnotations);
	}

//
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
		$this->chat->expects($this->once())
			->method('getCurrentUser')
			->will($this->returnValue($currentUser));

		$this->chat->expects($this->once())
			->method('getContacts')
			->will($this->returnValue($contacts));

		$this->chat->expects($this->once())
			->method('getBackends')
			->will($this->returnValue($backends));

		$this->chat->expects($this->once())
			->method('getInitConvs')
			->will($this->returnValue($initConvs));

		$this->greet->expects($this->once())
			->method('setRequestData')
			->with($greetRequestData)
			->will($this->returnValue(true));

		$this->greet->expects($this->once())
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
//
//
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
		$this->chat->expects($this->once())
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