<?php
/**
 * ownCloud - Chat app
 *
 * @author Tobia De Koninck (LEDfan)
 * @copyright 2013 Tobia De Koninck tobia@ledfan.be
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Chat\Controller;

use \OCA\Chat\Core\API;
use \OCP\AppFramework\Controller;
use \OCP\IRequest;
use \OCP\AppFramework\IAppContainer;
use \OCP\AppFramework\Http\JSONResponse;
use \OCA\Chat\Db\Backend;
use \OCA\Chat\Db\BackendMapper;


class AppController extends Controller {

    public function __construct(IAppContainer $app, IRequest $request){
		parent::__construct($app, $request);

	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function index() {
		$params = array(
		);
		return $this->render('main', $params);
	}
	
	/**
	 * @NoAdminRequired
	 */
	public function getContacts() {
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
	    return new JSONResponse(array('contacts' => $receivers, 'contactsList' => $contactList));
	}
	
	public function backend(){
		$backendMapper = new BackendMapper($this->app->getCoreApi());
		$backend = new Backend();
		$backend->setId($this->params('id'));
		
		if($this->params('do') === 'enable'){
			$backend->setEnabled('true');	
		} elseif($this->params('do') === 'disable'){
			$backend->setEnabled('false');
		}

		$backendMapper->update($backend);
		return new JSONResponse(array("status" => "success"));
	}

	/**
	 * @NoAdminRequired
	 */
	public function getBackends(){
		$backendMapper = new BackendMapper($this->app->getCoreApi());
		$backends = $backendMapper->getAllEnabled();

		return new JSONResponse(array("backends" => $backends));
	}

}
