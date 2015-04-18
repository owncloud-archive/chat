<?php
/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Chat\App;

class ChatTest extends \PHPUnit_Framework_TestCase {

	public function setUp(){
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

		$this->chat =  new Chat(
			$this->backendManager,
			$this->userOnlineMapper,
			$this->syncOnline,
			$this->user,
			$this->contactsManager,
			$this->rootFolder
		);


	}

	public function contactsProvider() {
		return array(
			array(
				array(
					'foo'
				),
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
	public function testGetContacts($onlineUsers, $rawContacts, $parsedContacts) {
		$this->userOnlineMapper->expects($this->any())
			->method('getOnlineUsers')
			->will($this->returnValue($onlineUsers));

		$this->och->expects($this->any())
			->method('getId')
			->will($this->returnValue('och'));

		$this->och->expects($this->any())
			->method('getDisplayName')
			->will($this->returnValue('ownCloud Chat'));

		$this->backendManager->expects($this->any())
			->method('getBackendByProtocol')
			->will($this->returnValue($this->och));

		$this->contactsManager->expects($this->any())
			->method('search')
			->will($this->returnValue($rawContacts));

		$result = $this->chat->getContacts();
		$this->assertEquals($parsedContacts, $result);
	}


	/**
	 * This test will run after the testGetContacts method
	 * This test will test if the cache is used instead of re-fetching the contacts information
	 *
	 * @depends      testGetContacts
	 * @dataProvider contactsProvider
//	 */
	public function testGetContactsCache($onlineUsers, $rawContacts, $parsedContacts) {
		$this->contactsManager->expects($this->never())
			->method('search');

		$result = $this->chat->getContacts();
		$this->assertEquals($parsedContacts, $result);
	}


	public function testGetBackends() {
		$this->backendManager->expects($this->once())
			->method('getEnabledBackends')
			->will($this->returnValue(array($this->och)));

		$expectedResult = array();
		$expectedResult[] = $this->och;

		$result = $this->chat->getBackends();
		$this->assertEquals($expectedResult, $result);
	}


	public function userContactProvider() {
		return array(
			array(
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
					'online' => 1,
					'order' => 1,
    				'saved' => 1,
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
	public function testGetUserasContact($rawContacts, $expectedResult, $UID) {
		$this->backendManager->expects($this->any())
			->method('getBackendByProtocol')
			->will($this->returnValue($this->och));

		$this->contactsManager->expects($this->any())
			->method('search')
			->will($this->returnValue($rawContacts));

		$result = $this->chat->getUserasContact($UID);
		$this->assertEquals($expectedResult, $result);
	}

}