<?php

namespace OCA\Chat\Core;

use OCA\Chat\DependencyInjection\DIContainer;
use OCP\AppFramework\IAppContainer;


/**
* Entry point for every request in your app. You can consider this as your
* public static void main() method
*
* Handles all the dependency injection, controllers and output flow
*/
class App {


        /**
         * Shortcut for calling a controller method and printing the result
         * @param string $controllerName the name of the controller under which it is
         * stored in the DI container
         * @param string $methodName the method that you want to call
         * @param DIContainer $container an instance of a pimple container.
         * @param array $urlParams list of URL parameters (optional)
         */
        public static function main($controllerName, $methodName, DIContainer $container, array $urlParams = null) {
                if (!is_null($urlParams)) {
                        $container['urlParams'] = $urlParams;
                }
                $controller = $container[$controllerName];

                // initialize the dispatcher and run all the middleware before the controller
                $dispatcher = $container['Dispatcher'];


                list($httpHeaders, $responseHeaders, $output) =
                        $dispatcher->dispatch($controller, $methodName);
//                throw new \Exception("main" . var_dump($output));


                if(!is_null($httpHeaders)) {
                        header($httpHeaders);
                }

                foreach($responseHeaders as $name => $value) {
                        header($name . ': ' . $value);
                }

                if(!is_null($output)) {
                        header('Content-Length: ' . strlen($output));
                        print($output);
                }

        }

        /**
         * Shortcut for calling a controller method and printing the result.
         * Similar to App:main except that no headers will be sent.
         * This should be used for example when registering sections via
         * \OC\AppFramework\Core\API::registerAdmin()
         *
         * @param string $controllerName the name of the controller under which it is
         * stored in the DI container
         * @param string $methodName the method that you want to call
         * @param array $urlParams an array with variables extracted from the routes
         * @param DIContainer $container an instance of a pimple container.
         */
        public static function part($controllerName, $methodName, array $urlParams,
                                                                DIContainer $container){

                $container['urlParams'] = $urlParams;
                $controller = $container[$controllerName];

                $dispatcher = $container['Dispatcher'];

                list(, , $output) = $dispatcher->dispatch($controller, $methodName);
                return $output;
        }

}