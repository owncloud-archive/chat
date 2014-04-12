<?php

namespace OCA\Chat\OCH\Commands;

include_once(__DIR__ . '/../../../autoloader.php');
include_once(__DIR__ . '/../../../vendor/Pimple/Pimple.php');


use OCA\Chat\Core\API;
use OCA\Chat\OCH\Commands\Join;
use OCA\Chat\App\Chat;
use \OCA\Chat\OCH\Db\UserOnline;
use OCA\Chat\Db\DBException;
use OCA\Chat\OCH\Exceptions\RequestDataInvalid;

// DONE
class JoinTest extends \PHPUnit_Framework_TestCase {
    
    
    public function setUp(){
		$app =  new Chat();
		$this->container = $app->getContainer();
		$this->container['API'] = $this->getMockBuilder('\OCA\Chat\Core\API')
			->disableOriginalConstructor()
			->getMock();
    	$this->container['API']->expects($this->any())
    		->method('log')
    		->will($this->returnValue(null));
    }
    
    /**
     * Testcase: if there is a PDOException in the datamapper a DBException must be thrown 
     * with the same message as in the PDOException
     */
    public function testDBFailure(){
    	$this->setExpectedException('\OCA\Chat\DB\DBException', 'Something went wrong with the DB!');
		// config
		$this->container['API']->expects($this->any())
			->method('prepareQuery')
			->will($this->throwException(new \PDOException('Something went wrong with the DB!')));
		
		// logic
		$join = new Join($this->container);
		$join->setRequestData(array(
		    "user" => array(
				"id" => "admin",
				"displayname"=> "admin",
				"backends" => array(
				    "och" => array(
						"id" => NULL,
						"displayname" => "wnCloud Handle",
						"protocol" => "x-owncloud-handle",
						"namespace" => "och",
						"value" => "admin"
		    		)
				),
				"address_book_id" => "admin",
				"address_book_backend"=> "localusers",
		    ),
		    "session_id" => "87ce2b3faeb92f0fb745645e7827f51a",
		    "timestamp" => 1397193430.516,
			"conv_id" => "dasdfwffws"
		));
		$result = $join->execute();
   }
   
	public function testOmmittedConvId(){
		$this->setExpectedException('\OCA\Chat\OCH\Exceptions\RequestDataInvalid', 'CONV-ID-MUST-BE-PROVIDED');
		
   		$this->container['API']->expects($this->any())
   			->method('prepareQuery')
   			->will($this->returnValue(true));
   	
	   	// logic
	   	$join = new Join($this->container);
	   	$join->setRequestData(array(
	   			"user" => array(
	   					"id" => "admin",
	   					"displayname"=> "admin",
	   					"backends" => array(
	   							"och" => array(
	   									"id" => NULL,
	   									"displayname" => "wnCloud Handle",
	   									"protocol" => "x-owncloud-handle",
	   									"namespace" => "och",
	   									"value" => "admin"
	   							)
	   					),
	   					"address_book_id" => "admin",
	   					"address_book_backend"=> "localusers",
	   			),
	   			"session_id" => "87ce2b3faeb92f0fb745645e7827f51a",
	   			"timestamp" => 1397193430.516,
	   			//"conv_id" => "dasdfwffws" => this is ommitted
	   	));
   }
    
}
