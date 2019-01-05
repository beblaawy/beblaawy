<?php

namespace Zarboxa\Core\Routing;

use Exception;
use App\Http\Kernel;
use Zarboxa\Core\Views\View;
use Zarboxa\Core\Framework\Request;
use Zarboxa\Core\Framework\Redirect;
use Zarboxa\Core\Routing\SingleRoute;

final class Route {

	private static $methods = ['get', 'post', 'put', 'patch', 'options', 'any', 'match'];

	private static $routes = [];

	/*
	* groupPrefix is used with group() function
	* It's used to prefix the routes with a specific prefix
	*/
	private static $groupPrefix = '';

	/*
	* groupPrefixes it's an array to hold all the group prefixes
	* it's a helper for the groupPrefix to set it's value
	* I need this property to handle the route defined after any group
	*/
	private static $groupPrefixes = [];

	/*
	* groupMiddleware is used with group() function
	* It's used to assign specific middlwares to the group routes
	*/
	private static $groupMiddleware = [];

	/*
	* groupMiddlewares it's an array to hold all the group middlewares
	* it's a helper for the groupMiddleware to set it's value
	* I need this property to handle the route defined after any group
	*/
	private static $groupMiddlewares = [];

	/*
	* groupNamespace is used with group() function
	* It's used to assign specific namespace to the group routes
	*/
	private static $groupNamespace = '';

	/*
	* groupNamespaces it's an array to hold all the group namespaces
	* it's a helper for the groupNamespace to set it's value
	* I need this property to handle the route defined after any group
	*/
	private static $groupNamespaces = [];

	/*
	* Current called route.
	*/
	private static $requestedRoute = '';

	/*
	* Current called route data.
	*/
	private static $requestedRouteData = null;

	/*
	* Handle all called static function with Route.
	* method is the called method name Route::name();
	*/
	public static function __callStatic($method, $arguments){
		if (!in_array($method, self::$methods)) {
		    throw new Exception("Invalid Method used with the Route", 1);
		}

		if (!isset($arguments) || count($arguments) != 2) {
		    throw new Exception("Invalid Arguments passed to the Route", 1);
		}

		$route     = $arguments[0];
		$callback  = $arguments[1];

		if( !is_callable($callback) ){
			if (!is_string($callback)) {
			    throw new Exception("Second argument must be a callable function", 1);
			}
		}
		return self::createSingleRoute($route, $method, $callback);
	}

	/*
	* get the single route.
	*/
	private static function createSingleRoute(String $route, $method, $callback){

		/* If the first character is not '/' ? then add it */
		if ($route != '/') {
			if (substr($route, 0, 1) != '/') {
				$route = '/' . $route;
			}
			$route = stripe_the_last_slash($route);
		}

		/*
		* Add all routes to routes array of SingleRoute objects
		*/
		$single = new SingleRoute($route, $callback, $method, self::$groupMiddleware, self::$groupNamespace);

		self::$routes[ self::$groupPrefix . $route ][ $method ] = $single;

		/*
		* return SingleRoute instance to handle the middleware stuff!
		*/
		return $single;
	}

	/*
	* Group Routes with a prefix and middleware.
	*/
	public static function group(array $params, $callback){

		/* Call the closure function to call the Route::? functions */
		if( !is_callable($callback) ){
		    throw new Exception("Second argument must be a callable function", 1);
		}
		
		/*
		* Push the last added group params
		*/
		self::groupParams($params);

		$callback();

		/*
		* pop the last added group params
		*/
		self::groupParams($params, true);
	}

	/*
	* Call group stuff which should be called :
	* before the group callback function.
	* after  the group callback function.
	*/
	public static function groupParams(array $params, $pop = false){

		/* 
		* Default $pop is false, and this mean it will push the new item to the specified array
		* When $pop is true, this mean it will pop the pushed item from the specified array
		*/
		self::handleGroupPrefixes($params, $pop);

		self::handleGroupMiddlewares($params, $pop);

		self::handleGroupNamespace($params, $pop);
	}

	/*
	* Push the prefix if create new group
	* Pop  the prefix when the group callback finish
	*/
	private static function handleGroupPrefixes($params, $pop = false){
		if (isset($params['prefix']) && !empty($params['prefix']) && is_string($params['prefix']) ) {
			if ($pop) {
				/*
				* after finish call the group closure :
				* pop the last index prefix
				*/
				array_pop(self::$groupPrefixes);
			}else{
				self::$groupPrefixes[] = '/' . str_replace('/', '', $params['prefix']);
			}
			/*
			* set the new prefix
			*/
			self::$groupPrefix = implode('', self::$groupPrefixes);
		}
	}

	/*
	* Push the middleware if create new group
	* Pop  the middleware when the group callback finish
	*/
	private static function handleGroupMiddlewares($params, $pop = false){
		if (isset($params['middleware']) && !empty($params['middleware']) ) {
			if ($pop) {
				/*
				* after finish call the group closure :
				* pop the last index middlware
				*/
				array_pop(self::$groupMiddlewares);
			}else{
				self::$groupMiddlewares[] = $params['middleware'];
			}
			/*
			* set the new middleware
			*/
			self::$groupMiddleware = merge_and_get_unique(
				flatten_function_arguments(self::$groupMiddlewares),
				$params['middleware']
			);
		}
	}


	/*
	* Push the namespace if create new group
	* Pop  the namespace when the group callback finish
	*/
	private static function handleGroupNamespace($params, $pop = false){
		if (isset($params['namespace']) && !empty($params['namespace']) && is_string($params['namespace']) ) {
			if ($pop) {
				/*
				* after finish call the group closure :
				* pop the last index prefix
				*/
				array_pop(self::$groupNamespaces);
			}else{
				self::$groupNamespaces[] = str_replace('/', '', $params['namespace']) . '\\';
			}
			/*
			* set the new prefix
			*/
			self::$groupNamespace = implode('', self::$groupNamespaces);
		}
	}

	/*
	* Requestd Url from the user.
	*/
	private static function requestedRoute(){
		if (!self::$requestedRoute) {
			self::$requestedRoute = stripe_the_last_slash( Request::url() );
		}
		return self::$requestedRoute;
	}

	private static function requestedRouteData(){
		if (!self::$requestedRouteData) {
			/*
			* This url function is defined in HelperFunctions file.
			*/
			if (!isset(self::$routes[ self::requestedRoute() ])) {
			    throw new Exception("The " . self::requestedRoute() . " is not defined!", 1);
			}
			self::$requestedRouteData = self::$routes[ self::requestedRoute() ][ Request::method() ];
		}
		return self::$requestedRouteData;
	}

	/*
	* This handle the last response to the user
	*/
	public static function returnResponseToUser(){

		/*
		* I have to check the middlwares first
		* if the value of route is callable, then call this function!
		* it will call the last defined key
		*/
		$route = self::requestedRouteData();

		/*
		* Call the Middlewares first before you return the response to the user
		*/
		(new Kernel)->handle($route->middleware);

		/*
		* check weather the called callback is callable function
		* or a controller function as a string
		*/

		if (!is_callable($route->callback) && is_string($route->callback)) {
			$data = explode("@", $route->callback);
			$name =  str_replace('/', '\\', CONTROLLER_FOLDER) . '\\' . $route->namespace . $data[0];
			$controller = new $name;
			if (isset($data[1]) && !empty($data[1])) {
				/*
				* The user may send the function name after the @ character
				*/
				$callback = $controller->{$data[1]}();
			}else{
				/*
				* If the user didn't send the function name after the 2 character
				* Then call the object itself, and this call must call the __invoke method
				* he must set it
				*/
				$callback = ($controller)();
			}
		}else{
			$callback = ($route->callback)();
		}

		echo self::handleTheResponseToBeEchoed( $callback );
	}

	/*
	* handle response to user, he may return a string or array or even an object!
	* or he can return a whole view
	*/
	private static function handleTheResponseToBeEchoed($response){
		if (is_string($response)) {
			return $response;
		}
		if ($response instanceof View) {
			View::display($response);
		}else{
			if (is_object($response) && method_exists($response, "toJson")) {
				return json_encode($response->toJson());
			}
			return $response ? json_encode($response) : null;
		}
	}
	public static function all(){
		echo "<pre>";
		print_r(self::$routes);
	}

	/*
	* get the current route url
	*/
	public static function current(){
		return self::requestedRouteData()->route;
	}

	/*
	* get the current route name, which defined with the name function
	*/
	public static function currentRouteName(){
		return self::requestedRouteData()->name;
	}

	/*
	* get the current route action ( method )
	*/
	public static function currentRouteAction(){
		return self::requestedRouteData()->method;
	}
}