<?php
namespace OCA\Chat\Core;

use \OCA\Chat\Db\Backend;
use \OCA\Chat\Db\BackendMapper;
use \OCP\AppFramework\IAppContainer;

class AppApi {
	
	protected $app;
	
	public function __construct(IAppContainer $app){
		$this->app = $app;
	}
	
	public function registerBackend($displayName, $name, $protocol, $enabled){
		$backendMapper = new BackendMapper($this->app->getCoreApi());
		if($backendMapper->exists($name)){
			// Only execute when there are no backends registered i.e. on first run
			$backend = new Backend();
			$backend->setDisplayname($displayName);
			$backend->setName($name);
			$backend->setProtocol($protocol);
			$backend->setEnabled($enabled);
			$backendMapper->insert($backend);
		}
	}
	
	public function getEnabledBackends(){
		$backendMapper = new BackendMapper($this->app->getCoreApi());
		return $backendMapper->getAll();
	}
	
	public function getContacts(){
		$cm = \OC::$server->getContactsManager();
		// The API is not active -> nothing to do
		if (!$cm->isEnabled()) {
			$receivers = null;
			$error = 'Please enable the contacts app.';
		}
		
		$result = $cm->search('',array('FN'));
		$receivers = array();
		$contactList = array();
		foreach ($result as $r) {
			$data = array();
		
			$contactList[] = $r['FN'];
		
			$data['id'] = $r['id'];
			$data['displayname'] = $r['FN'];
		
			if(isset($r['EMAIL'])){
				$email = $r['EMAIL'];
				if (!is_array($email)) {
					$email = array($email);
				}
				$data['email'] = $email;
			} else {
				$data['email'] = array();
			}
		
			if(isset($r['IMPP'])){
				$data['IMPP'] = array(); // Array with all IM contact information
				foreach($r['IMPP'] as $IMPP){
					$array = array();
					$exploded = explode(":", $IMPP);
					if($exploded[0] === 'owncloud-handle'){
						$array['backend'] = 'ownCloud';
					} else {
						$array['backend'] = $exploded[0];
					}
					$array['value'] = $exploded[1];
					$data['IMPP'][] = $array;
				}
			} else {
				$data['IMPP'] = array();
			}
			$receivers[] = $data;
		}
		return array('contacts' => $receivers, 'contactsList' => $contactList);
	}
	
	public function getBackends(){
		$backendMapper = new BackendMapper($this->app->getCoreApi());
		$backends = $backendMapper->getAllEnabled();
	
		return $backends;
	}
}