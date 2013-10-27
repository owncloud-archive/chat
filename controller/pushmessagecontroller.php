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

use \OCA\AppFramework\Controller\Controller;
use \OCA\AppFramework\Core\API;
use \OCA\AppFramework\Http\Request;
use \OCA\AppFramework\Http\Response;
use \OCA\AppFramework\Http\JSONResponse;
use \OCA\Chat\Db\PushMessage;
use \OCA\Chat\Db\PushMessageMapper;
use \OCA\Appframework\Db\DoesNotExistException;


class PushMessageController extends Controller {

	public function __construct(API $api, Request $request){
		parent::__construct($api, $request);
	}

	/**
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 * @CSRFExemption
	 * 
	 */
	public function get() {
		session_write_close();	// Very important! http://stackoverflow.com/questions/11946638/long-polling-blocks-my-other-ajax-requests
		
		try{
			$mapper = new PushMessageMapper($this->api); // inject API class for db access
			$pushMessage = $mapper->findByReceiver($this->params('receiver'));		
		} catch(DoesNotExistException $e){
			sleep(1);
			$this->get();
		}
		
		$mapper = new PushMessageMapper($this->api); // inject API class for db access
		$pushMessage = $mapper->findByReceiver($this->params('receiver'));
		return new JSONResponse(array('status' => 'command', 'id' => $pushMessage->getId(), 'data' => json_decode($pushMessage->getCommand())));
		
	}
	
	/**
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 * @CSRFExemption
	 */
	public function delete(){
		$pushMessage = new PushMessage();
		$pushMessage->setId($this->params('id'));
		$mapper = new PushMessageMapper($this->api);
		$mapper->delete($pushMessage);
		
		return new JSONResponse(array('status' => 'success'));
	}

}
