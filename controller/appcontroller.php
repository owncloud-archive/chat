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

use \OCP\AppFramework\Controller;
use \OCP\IRequest;
use \OCP\AppFramework\IAppContainer;
use \OCP\AppFramework\Http\JSONResponse;
use \OCA\Chat\Db\Backend;
use \OCA\Chat\Db\BackendMapper;


class AppController extends Controller {

    public function __construct($appName, IRequest $request, IAppContainer $app){
	parent::__construct($appName, $request);
	$this->app = $app;
    }
    
    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function index() {
        $appApi = $this->app['AppApi'];
        $contacts = $appApi->getContacts();
        $backends = $appApi->getBackends();
        $initConvs = $appApi->getInitConvs();

        $params = array(
            "initvar" => json_encode(array(	
                "contacts" => $contacts['contacts'],
                "contactsList" => $contacts['contactsList'],
		"contactsObj" => $contacts['contactsObj'],
                "backends" => $backends,
                "initConvs" => $initConvs
            ))
        );
        return $this->render('main', $params);
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
}
