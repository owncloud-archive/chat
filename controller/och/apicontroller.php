<?php

namespace OCA\Chat\Controller\OCH;
use \OCA\Chat\OCH\Responses\Success;
use \OCA\Chat\OCH\Responses\Error;
use \OCA\Chat\OCH\Exceptions\RequestDataInvalid;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Controller;
use \OCP\IRequest;
use \OCP\AppFramework\IAppContainer;

class ApiController extends Controller {	

    public function __construct($appName, IRequest $request, IAppContainer $app){
	parent::__construct($appName, $request);
	$this->app = $app;
    }  
    /**
     * Routes the API Request
     * @param String $this->params('JSON') command in JSON
     * @return JSONResponse 
     * @NoAdminRequired
     */
    public function route(){
        $request = json_decode($this->params('JSON'), true);
        list($requestType, $action, $http_type) = explode("::", $request['type']);

        if($http_type === "request"){
            // Check request type 
            switch($requestType){
                case "command":
                        // $action is the type of the command

                        $possibleCommands = array('greet', 'join', 'invite', 'send_chat_msg', 'quit', 'online', 'start_conv');
                        if(in_array($action, $possibleCommands)){
                            if(!empty($request['data']['session_id'])){
                                if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){

                                    $commandClasses = array(
                                        'greet' => '\OCA\Chat\OCH\Commands\Greet',
                                        'join' => '\OCA\Chat\OCH\Commands\Join',
                                        'invite' => '\OCA\Chat\OCH\Commands\Invite',
                                        'send_chat_msg' => '\OCA\Chat\OCH\Commands\SendChatMsg',
                                        'quit' => '\OCA\Chat\OCH\Commands\Quit',
                                        'online' => '\OCA\Chat\OCH\Commands\Online',
                                        'start_conv' => '\OCA\Chat\OCH\Commands\StartConv'
                                    );

                                    try{
                                        $className = $commandClasses[$action];
                                        $commandClass = new $className($this->app->getCoreApi());
                                        $commandClass->setRequestData($request['data']);
                                        $data = $commandClass->execute();
                                        if($data){
                                            return new Success("command", $action, $data);
                                        } else {
                                            return new Success("command", $action);
                                        }
                                    }catch(RequestDataInvalid $e){
                                        return new Error("command", $action, $e->getMessage());
                                    }
                                } else {
                                    return new Error("command", $action, "USER-NOT-EQUAL-TO-OC-USER");
                                }
                            } else {
                                return new Error("command", $action, "SESSION-ID-NOT-PROVIDED"); 
                            }
                        } else {
                            return new Error("command", $action, "COMMAND-NOT-FOUND");
                        }

                        break;
                case "push":
                    if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
                        if(!empty($request['data']['session_id'])){
                            //throw new \Exception("good to go");
                            $pushClasses = array(
                                "get" => "\OCA\Chat\OCH\Push\Get",
                                "delete" => "\OCA\Chat\OCH\Push\Delete"
                            );
                            $className = $pushClasses[$action];
                            $pushClass = new $className($this->app->getCoreApi());
                            //throw new \Exception("ok");

                            $pushClass->setRequestData($request['data']);

                            return $pushClass->execute();
                            //throw new \Exception("ok");

                        } else{
                            return new Error('push', 'session_id not provided');
                            //@todo add better error reporting
                        }
                    } else {
                        return new Error('push', 'user not ok');
                    }
                    break;
                case "data":
		    if($request['data']['user']['backends']['och']['value'] === $this->app->getCoreApi()->getUserId()){
                        if(!empty($request['data']['session_id'])){
                            $pushClasses = array(
                                "messages" => "\OCA\Chat\OCH\Data\Messages",
                            );
                            $className = $pushClasses[$action];
                            $dataClass = new $className($this->app->getCoreApi());

                            $dataClass->setRequestData($request['data']);

                            $data = $dataClass->execute();
			    if($data){
				return new Success("command", $action, $data);
			    } else {
				return new Success("command", $action);
			    }
                        } else{
                            return new Error('push', 'session_id not provided');
                            //@todo add better error reporting
                        }
                    } else {
                        return new Error('push', 'user not ok');
                    }
                    break;
            }

        } else {
            return new Error($requestType, $action, "HTTP-TYPE-INVALID");
        }
    }
}