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
namespace Ratchet\Tutorials;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;



class Chat implements MessageComponentInterface {
    protected $clients;
    protected $rooms;
    protected $users;
    protected $OC_user;
    
    public function __construct($OC_user) {
        $this->clients = new \SplObjectStorage;
        $this->rooms = array();
        $this->users = array();
        $this->OC_user = $OC_user;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
    } 
    
    /**
     * @brief This is the core function of the Chat server, each request is handled by this function
     * @param ConnectionInterface $from object which stores the connection with the client
     * @param JSON $msg Message send by the client
    */
    public function onMessage(ConnectionInterface $from, $msg) {
       
    	
        $commands = array(  'join' => 'join',
                            'send' => 'send',
                            'getusers' => 'getusers',
                            'invite' => 'invite',
                            'greet' => 'greet',
                            'leave' => 'leave');
        
        $msgJSON = json_decode($msg, true);
        
        
        if($msgJSON['status'] == 'command'){
            if(isset($msgJSON['data']) && in_array($msgJSON['data']['type'], $commands)){
            	// Call the function
                $this->$commands[$msgJSON['data']['type']]($from, $msgJSON['data']['param'], $msg, $msgJSON['id']);
            }
        }
    
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
    	$conn->close();
    }
    
	
    /**
     * @brief Called when the client closes the connection
     * @param ConnectionInterface $from object which stores the connection with the client
     */
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        
        $key = array_search($conn, $this->users);
        
        unset($this->users[$key]);
        
        var_dump(array_keys($this->users));
        
    }

    /** 
     * @brief Called when the user leaves a conservation
     * @param ConnectionInterface $from object which stores the connection with the client
     * @param array $param array with all the paramters given in the JSON message send by the user
     * @param JSON $raw Originally message send by the user
     * @param string $id the id of the message send by the user 
     * */
    private function leave($from, $param, $raw, $id){

        $key = array_search($param['user'], $this->rooms[$param['conservationID']]['users'] );
        unset($this->rooms[$param['conservationID']]['users'][$key]);
        $key = array_search($from, $this->rooms[$param['conservationID']]['contacts']); 
        unset($this->rooms[$param['conservationID']]['contacts'][$key]);
        
        //If there is only one user left in the conservation, the conservation has to be deleted
        if(count($this->rooms[$param['conservationID']]['contacts']) <= 1){
            unset($this->rooms[$param['conservationID']]);
        }
        foreach($this->rooms[$param['conservationID']]['contacts'] as $user){
            $user->send(json_encode(array(
                                                "status" => "command",
                								"data" => array(
                										"type" => "left",
                										"param" => array(
                												"user" => $param['user'],
                												"conservationID" => $param['conservationID'],
                                                                "deleteRoom" => $deleteRoom
                												)))));
        }          
        $from->send(json_encode(array(	"status" => "success", 
        								"id" => $id)));       
    }
    
    /**
     * @brief The very first command sent by the client to the server when the connection between the client and the server is established  
     * @param ConnectionInterface $from object which stores the connection with the client
     * @param array $param array with all the paramters given in the JSON message send by the user
     * @param JSON $raw Originally message sent by the user
     * @param string $id the id of the message sent by the user
     * */
    private function greet($from, $param, $raw, $id){
    	
        // First check if the user is a real owncloud user
        if (in_array($param['user'], $this->OC_user->getUsers())){
            $this->users[$param['user']] = $from; // Add the connected user to the users array
            $from->send(json_encode(array("status" => "success", "id" => $id)));
        } else {
            $from->send(json_encode(array(  "status" => "error",
                                            "data"   =>  array(
                                                                "msg" => "NotOCUser",
                                                            ),
                                            "id" => $id)));
        }
        var_dump(array_keys($this->users));
        
    }
    
    /**
     * @brief Invites a user to a conservation
     * @param ConnectionInterface $from object which stores the connection with the client
     * @param array $param array with all the paramters given in the JSON message send by the user
     * @param JSON $raw Originally message send by the user
     * @param string $id the id of the message send by the user
     * */
    private function invite($from, $param, $raw, $id){
    // TODO: check if user is already in a conservation
    
    
    // First check if the userToInvite is a real OC username
        if (in_array($param['userToInvite'], $this->OC_user->getUsers()) && in_array($param['userToInvite'], array_keys($this->users))){
            $this->users[$param['userToInvite']]->send($raw);
            $from->send(json_encode(array(  "status" => "success", "id" => $id)));
        }else {
            if( in_array($param['userToInvite'], $this->OC_user->getUsers()) && !in_array($param['userToInvite'], array_keys($this->users))){
                   $from->send(json_encode(array(  "status" => "error",
                                                    "id" => $id,
                                                "data"   =>  array(
                                                    "msg" => "usernotonline"
                                        ))));
            } else {
                $from->send(json_encode(array(  "status" => "error",
                                                                    "id" => $id,

                                                "data"   =>  array(
                                                    "msg" => "usernotexists"
                                        ))));
            }
                                                                
        }
        

    }
    
    /**
     * @brief 
     * @param ConnectionInterface $from object which stores the connection with the client
     * @param array $param array with all the paramters given in the JSON message send by the user
     * @param JSON $raw Originally message send by the user
     * @param string $id the id of the message send by the user
     * */
    private function getusers($from, $param, $raw, $id){
        $users = $this->rooms[$param['conservationID']]['users'];
        
        $send = array(
                "status" => "success",
                "data" => array(
                        "type" => "getusers",
                        "param" => array (
                            "room" => $param['conservationID'],
                            "users" => array_values($users)
                            )
                ), 
                "id" => $id
            
            );
            
        $send = json_encode($send);
        $from->send($send);
     
    }
    
    private function send($from, $param, $raw, $id){
        // All the users are stored in a array as objects, this aray is stored in the $this->rooms array, the key of this array is the name of the room
        
        // TODO check if $param['user'] (the sender of the message) is in the conservation, and if it's a real owncloud user
        if(in_array($param['user'], $this->OC_user->getUsers())){
             if(in_array($param['user'], $this->rooms[$param['conservationID']]['users'])){ 
                foreach ($this->rooms[$param['conservationID']]['contacts'] as $contact) {
                    $contact->send($raw);
                    echo $raw . "\n";
                }
                $from->send(json_encode(array("status"=>"success", "id" =>$id)));
            } else {
                $from->send(json_encode(array("status"=>"error", "id" => $id, "data" => array("msg" => "USERNOTINCONSERVATION"))));
            } 
        } else {
           $from->send(json_encode(array("status"=>"error", "id" => $id, "data" => array("msg" => "USERNOTREALUSER"))));
        }

    }
    
    
    
    private function join($from, $param, $raw, $id){      
        if (empty($this->rooms[$param['conservationID']])){
            
            // Conversation doesn't exists
            
            $contacts = array();
            $users = array();
            
            array_push($contacts, $from); // Add the currently joined user to the array with contacts
            $users[] = $param['user'];
            $this->rooms[$param['conservationID']] = array('contacts' => $contacts,
                                                 'users' => $users );
            
            $from->send(json_encode(array("status"=>"success", "id" =>$id)));
            // Connected 
                                    
            
                        
        } else {
            // Conversation already exists
         
            // This is a public conversation
            $contacts = $this->rooms[$param['conservationID']]['contacts'];
            $users = $this->rooms[$param['conservationID']]['users'];

            $users[] = $param['user'];

            array_push($contacts, $from); // Add the currently joined user to the array with contacts
            
            $this->rooms[$param['conservationID']]['contacts'] = $contacts; // Updat array with users
            $this->rooms[$param['conservationID']]['users'] = $users; 

            $from->send(json_encode(array("status"=>"success", "id" =>$id)));
                
            
        
    
           
        }       

    }
}