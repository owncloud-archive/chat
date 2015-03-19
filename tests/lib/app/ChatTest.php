<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

use OCA\Chat\OCH\OCH;

class ChatTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \OCA\Chat\App\Chat
	 */
	public $app;

	public function contactsProvider() {
		$app = new Chat(array(), true);
		$OCHBackend = return new OCH(
			$c->query('ConfigMapper'),
			$c->query('OCP\IConfig'),
			$c->query('UserMapper'),
			$c->query('AttachmentMapper'),
			$c->query('StartConvCommand'),
			$c->query('MessagesData'),
			$c->query('JoinCommand'),
			$app
		);
		return array(
			array(
				$app,
				array(
					'foo'
				),
				$OCHBackend,
				array(
					0 => array(
						'id' => 'foo',
						'FN' => 'foo',
						'EMAIL' => array(),
						'IMPP' => array(
							0 => 'x-owncloud-handle:foo',
						),
						'addressbook-key' => 'local',
					),
					1 => array(
						'id' => 'bar',
						'FN' => 'bar',
						'EMAIL' => array(),
						'IMPP' => array(
							0 => 'x-owncloud-handle:bar',
						),
						'addressbook-key' => 'local',
					),
					2 => array(
						'id' => '1',
						'N' => array(
							0 => '',
							1 => 'TestContact',
							2 => '',
							3 => '',
							4 => '',
						),
						'UID' => '1a2a30d7-4907-4d5c-8e4a-3e51cf89e55a@localhost',
						'FN' => 'TestContact',
						'addressbook-key' => 'local:1',
					)
				),
				array(
					'contacts' =>
						array(
							0 =>
								array(
									'id' => 'foo',
									'online' => true,
									'displayname' => 'foo',
									'order' => 1,
									'saved' => true,
									'backends' =>
										array(
											0 =>
												array(
													'id' => 'email',
													'displayname' => 'E-mail',
													'protocol' => 'email',
													'namespace' => ' email',
													'value' =>
														array(
															0 =>
																array(),
														),
												),
											1 =>
												array(
													'id' => 'och',
													'displayname' => 'ownCloud Chat',
													'protocol' => 'x-owncloud-handle',
													'namespace' => 'och',
													'value' => 'foo',
												),
										),
									'address_book_id' => '',
									'address_book_backend' => 'local',
								),
							1 =>
								array(
									'id' => 'bar',
									'online' => false,
									'displayname' => 'bar',
									'order' => 2,
									'saved' => true,
									'backends' =>
										array(
											0 =>
												array(
													'id' => 'email',
													'displayname' => 'E-mail',
													'protocol' => 'email',
													'namespace' => ' email',
													'value' =>
														array(
															0 =>
																array(),
														),
												),
											1 =>
												array(
													'id' => 'och',
													'displayname' => 'ownCloud Chat',
													'protocol' => 'x-owncloud-handle',
													'namespace' => 'och',
													'value' => 'bar',
												),
										),
									'address_book_id' => '',
									'address_book_backend' => 'local',
								),
							2 =>
								array(
									'id' => '1',
									'online' => false,
									'displayname' => 'TestContact',
									'order' => 3,
									'saved' => true,
									'backends' =>
										array(
											0 =>
												array(
													'id' => 'email',
													'displayname' => 'E-mail',
													'protocol' => 'email',
													'namespace' => ' email',
													'value' =>
														array(
															0 =>
																array(),
														),
												),
										),
									'address_book_id' => '1',
									'address_book_backend' => 'local',
								),
						),
					'contactsList' =>
						array(
							0 => 'foo',
							1 => 'bar',
							2 => '1',
						),
					'contactsObj' =>
						array(
							'foo' =>
								array(
									'id' => 'foo',
									'online' => true,
									'displayname' => 'foo',
									'order' => 1,
									'saved' => true,
									'backends' =>
										array(
											0 =>
												array(
													'id' => 'email',
													'displayname' => 'E-mail',
													'protocol' => 'email',
													'namespace' => ' email',
													'value' =>
														array(
															0 =>
																array(),
														),
												),
											1 =>
												array(
													'id' => 'och',
													'displayname' => 'ownCloud Chat',
													'protocol' => 'x-owncloud-handle',
													'namespace' => 'och',
													'value' => 'foo',
												),
										),
									'address_book_id' => '',
									'address_book_backend' => 'local',
								),
							'bar' =>
								array(
									'id' => 'bar',
									'online' => false,
									'displayname' => 'bar',
									'order' => 2,
									'saved' => true,
									'backends' =>
										array(
											0 =>
												array(
													'id' => 'email',
													'displayname' => 'E-mail',
													'protocol' => 'email',
													'namespace' => ' email',
													'value' =>
														array(
															0 =>
																array(),
														),
												),
											1 =>
												array(
													'id' => 'och',
													'displayname' => 'ownCloud Chat',
													'protocol' => 'x-owncloud-handle',
													'namespace' => 'och',
													'value' => 'bar',
												),
										),
									'address_book_id' => '',
									'address_book_backend' => 'local',
								),
							1 =>
								array(
									'id' => '1',
									'online' => false,
									'displayname' => 'TestContact',
									'order' => 3,
									'saved' => true,
									'backends' =>
										array(
											0 =>
												array(
													'id' => 'email',
													'displayname' => 'E-mail',
													'protocol' => 'email',
													'namespace' => ' email',
													'value' =>
														array(
															0 =>
																array(),
														),
												),
										),
									'address_book_id' => '1',
									'address_book_backend' => 'local',
								),
						),
				)
			)
		);
	}

	/**
	 * @dataProvider contactsProvider
	 */
	public function testGetContacts(Chat $app, $onlineUsers, $OCHBackend, $rawContacts, $parsedContacts) {
		$app->c['UserOnlineMapper'] = $this->getMockBuilder('\OCA\Chat\OCH\Db\UserOnlineMapper')
			->disableOriginalConstructor()
			->getMock();
		$app->c['UserOnlineMapper']->expects($this->any())
			->method('getOnlineUsers')
			->will($this->returnValue($onlineUsers));

		$app->c['BackendManager'] = $this->getMockBuilder('\OCA\Chat\BackendManager')
			->disableOriginalConstructor()
			->getMock();
		$app->c['BackendManager']->expects($this->any())
			->method('getBackendByProtocol')
			->will($this->returnValue($OCHBackend));


		$app->c['SyncOnlineCommand'] = $this->getMockBuilder('\OCA\Chat\OCH\Commands\SyncOnline')
			->disableOriginalConstructor()
			->getMock();

		$app->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
			->disableOriginalConstructor()
			->getMock();

		$app->c['ContactsManager']->expects($this->any())
			->method('search')
			->will($this->returnValue($rawContacts));

		$result = $app->getContacts();
		$this->assertEquals($parsedContacts, $result);


	}


	/**
	 * This test will run after the testGetContacts method
	 * This test will test if the cache is used instead of re-fetching the contacts information
	 *
	 * @depends      testGetContacts
	 * @dataProvider contactsProvider
	 */
	public function testGetContactsCache($onlineUsers, $OCHBackend, $rawContacts, $parsedContacts) {
		$app->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
			->disableOriginalConstructor()
			->getMock();

		$app->c['ContactsManager']->expects($this->never())
			->method('search');

		$result = $app->getContacts();
		$this->assertEquals($parsedContacts, $result);
	}

	public function backendProvider() {
		$app = new Chat(array(), true);
		$OCHBackend = $app->query('OCH');
		return array(
			array(
				$app,
				$OCHBackend
			)
		);
	}

	/**
	 * @dataProvider backendProvider
	 */
	public function testGetBackends($app, $backend) {
		$app->c['BackendManager'] = $this->getMockBuilder('\OCA\Chat\BackendManager')
			->disableOriginalConstructor()
			->getMock();

		// Mock the exist method so, that it returns true
		$app->c['BackendManager']->expects($this->once())
			->method('getEnabledBackends')
			->will($this->returnValue(array($backend)));

		$expectedResult = array();
		$expectedResult[] = $backend;

		$result = $app->getBackends();

		$this->assertEquals($expectedResult, $result);

	}


	public function userContactProvider() {
		$app = new Chat(array(), true);
		$OCHBackend = $app->query('OCH');
		return array(
			array(
				$app,
				$OCHBackend,
				array(
					0 => array(
						'id' => 'foo',
						'FN' => 'foo',
						'EMAIL' => array(),
						'IMPP' => array(
							0 => 'x-owncloud-handle:foo',
						),
						'addressbook-key' => 'local',
					),
					1 => array(
						'id' => 'bar',
						'FN' => 'bar',
						'EMAIL' => array(),
						'IMPP' => array(
							0 => 'x-owncloud-handle:bar',
						),
						'addressbook-key' => 'local',
					),
					2 => array(
						'id' => '1',
						'N' => array(
							0 => '',
							1 => 'TestContact',
							2 => '',
							3 => '',
							4 => '',
						),
						'UID' => '1a2a30d7-4907-4d5c-8e4a-3e51cf89e55a@localhost',
						'FN' => 'TestContact',
						'addressbook-key' => 'local:1',
					)
				),
				array(
					'id' => 'foo',
					'displayname' => 'foo',
					'backends' =>
						array(
							0 =>
								array(
									'id' => 'email',
									'displayname' => 'E-mail',
									'protocol' => 'email',
									'namespace' => ' email',
									'value' =>
										array(
											0 =>
												array(),
										),
								),
							1 =>
								array(
									'id' => 'och',
									'displayname' => 'ownCloud Chat',
									'protocol' => 'x-owncloud-handle',
									'namespace' => 'och',
									'value' => 'foo',
								),
						),
					'address_book_id' => '',
					'address_book_backend' => 'local',
				),
				'foo'
			)
		);
	}

	/**
	 * @dataProvider userContactProvider
	 */
	public function testGetUserasContact($app, $OCHBackend, $rawContacts, $expectedResult, $UID) {
		$app->c['BackendManager'] = $this->getMockBuilder('\OCA\Chat\BackendManager')
			->disableOriginalConstructor()
			->getMock();
		$app->c['BackendManager']->expects($this->any())
			->method('getBackendByProtocol')
			->will($this->returnValue($OCHBackend));

		$app->c['ContactsManager'] = $this->getMockBuilder('\OC\ContactsManager')
			->disableOriginalConstructor()
			->getMock();

		$app->c['ContactsManager']->expects($this->any())
			->method('search')
			->will($this->returnValue($rawContacts));

		$result = $app->getUserasContact($UID);
		$this->assertEquals($expectedResult, $result);
	}

//	public function initConvsProvider(){
//		$conv1 = new User();
//		$conv1->setConversationId('CONV_ID_1408002874_42');
//		$conv1->setUser('admin');
//		$conv1->setJoined(329499626);
//		$conv1->setId(1);
//
//		$conv2 = new User();
//		$conv2->setConversationId('CONV_ID_1408002874_31');
//		$conv2->setUser('admin');
//		$conv2->setJoined(329443626);
//		$conv2->setId(2);
//
//		$conv3 = new User();
//		$conv3->setConversationId('CONV_ID_1408002874_26');
//		$conv3->setUser('admin');
//		$conv3->setJoined(324529443626);
//		$conv3->setId(3);
//
//		$currentUser = array(
//			'id' => 'admin',
//			'displayname' => 'admin',
//			'backends' => array (
//				'email' => array (
//					'id' => NULL,
//					'displayname' => 'E-mail',
//					'protocol' => 'email',
//					'namespace' => ' email',
//					'value' => array (
//						0 => array (
//						),
//					),
//				),
//				'och' => array (
//					'id' => NULL,
//					'displayname' => 'ownCloud Handle',
//					'protocol' => 'x-owncloud-handle',
//					'namespace' => 'och',
//					'value' => 'admin',
//				),
//			),
//			'address_book_id' => 'local',
//			'address_book_backend' => '',
//		);
//
//		$joinRequestData1 = array(
//			"conv_id" => "CONV_ID_1408002874_42",
//			"user" => $currentUser
//		);
//
//		$joinRequestData2 = array(
//			"conv_id" => "CONV_ID_1408002874_31",
//			"user" => $currentUser
//		);
//
//		$joinRequestData3 = array(
//			"conv_id" => "CONV_ID_1408002874_26",
//			"user" => $currentUser
//		);
//
//
//		return array(
//			array(
//				array(
//					$conv1,
//					$conv2,
//					$conv3
//				),
//				array(
//					$joinRequestData1,
//					$joinRequestData2,
//					$joinRequestData3,
//				),
//				'admin'
//			)
//		);
//	}


}