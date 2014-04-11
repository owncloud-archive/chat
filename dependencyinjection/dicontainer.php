<?php
namespace OCA\Chat\DependencyInjection;

use \OCA\Chat\Core\API;
use \OCA\Chat\Core\Http;
use \OCA\Chat\Core\Http\Dispatcher;
use \OCA\Chat\Core\Middleware\MiddlewareDispatcher;
use \OCA\Chat\Core\Middleware\Security\SecurityMiddleware;
use \OCA\Chat\Core\Utility\SimpleContainer;
use \OCA\Chat\Core\Utility\TimeFactory;
use \OCA\Chat\Controller\AppController;
use \OCA\Chat\Controller\OCH\ApiController;
use \OCA\Chat\Core\AppApi;

use \OCP\AppFramework\Http\Request;
use \OCP\AppFramework\IApi;
use \OCP\AppFramework\IAppContainer;
use \OCP\AppFramework\IMiddleWare;
use \OCP\AppFramework\Middleware;
use \OCP\IServerContainer;

class DIContainer extends SimpleContainer implements IAppContainer{

    /**
     * @var array
     */
    private $middleWares = array();

    /**
     * Put your class dependencies in here
     * @param string $appName the name of the app
     */
    public function __construct($appName, $urlParams = array()){
        $this['AppName'] = $appName;
        $this['urlParams'] = $urlParams;

        $this->registerParameter('ServerContainer', \OC::$server);

        $this['API'] = $this->share(function($c){
            return new API($c['AppName']);
        });

        /**
         * Http
         */
        $this['Request'] = $this->share(function($c) {
            /** @var $c SimpleContainer */
            /** @var $server IServerContainer */
            $server = $c->query('ServerContainer');
            $server->registerParameter('urlParams', $c['urlParams']);
            return $server->getRequest();
        });

        $this['Protocol'] = $this->share(function($c){
            if(isset($_SERVER['SERVER_PROTOCOL'])) {
                    return new Http($_SERVER, $_SERVER['SERVER_PROTOCOL']);
            } else {
                    return new Http($_SERVER);
            }
        });

        $this['Dispatcher'] = $this->share(function($c) {
            return new Dispatcher($c['Protocol'], $c['MiddlewareDispatcher']);
        });

        /**
         * Middleware
         */
        $app = $this;
        $this['SecurityMiddleware'] = $this->share(function($c) use ($app){
            return new SecurityMiddleware($app, $c['Request']);
        });

        $middleWares = $this->middleWares;
        $this['MiddlewareDispatcher'] = $this->share(function($c) use ($middleWares) {
            $dispatcher = new MiddlewareDispatcher();
            $dispatcher->registerMiddleware($c['SecurityMiddleware']);

            foreach($middleWares as $middleWare) {
                    $dispatcher->registerMiddleware($middleWare);
            }

            return $dispatcher;
        });

        /**
         * Utilities
         */
        $this['TimeFactory'] = $this->share(function($c){
            return new TimeFactory();
        });

        $this['this'] = $this;

	$this['AppController'] = $this->share(function($c){
            return new AppController($c['AppName'], $c['Request'], $c['this']);
	});

 

	$this['ApiController'] = $this->share(function($c){
	    return new ApiController($c['AppName'], $c['Request'], $c['this']);
	});
	
	 $this['AppApi'] = $this->share(function($c){    
            return new AppApi($c['this']);
        });
    }

    /**
     * @return IApi
     */
    public function getCoreApi(){
        return $this->query('API');
    }
    

    /**
     * @return \OCP\IServerContainer
     */
    function getServer(){
        return $this->query('ServerContainer');
    }

    /**
     * @param Middleware $middleWare
     * @return boolean
     */
    function registerMiddleWare(Middleware $middleWare){
        array_push($this->middleWares, $middleWare);
    }

    /**
     * used to return the appname of the set application
     * @return string the name of your application
     */
    function getAppName(){
        return $this->query('AppName');
    }

    /**
     * @return boolean
     */
    public function isLoggedIn(){
        return \OC_User::isLoggedIn();
    }

    /**
     * @return boolean
     */
    function isAdminUser(){
        $uid = $this->getUserId();
        return \OC_User::isAdminUser($uid);
    }

    private function getUserId(){
        return \OC::$session->get('user_id');
    }

    /**
     * @param $message
     * @param $level
     * @return mixed
     */
    function log($message, $level) {
        switch($level){
            case 'debug':
                $level = \OCP\Util::DEBUG;
                break;
            case 'info':
                $level = \OCP\Util::INFO;
                break;
            case 'warn':
                $level = \OCP\Util::WARN;
                break;
            case 'fatal':
                $level = \OCP\Util::FATAL;
                break;
            default:
                $level = \OCP\Util::ERROR;
                break;
        }
        \OCP\Util::writeLog($this->getAppName(), $message, $level);
    }
}
