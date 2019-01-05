<?php

namespace Zarboxa\Core\Routing;

final class SingleRoute {

	/*
	* Route url name
	*/
	public $route = '';

	/*
	* Route name
	*/
	public $name = '';

	/*
	* Route Callback
	*/
	public $callback = null;

	/*
	* Route Middlewares
	*/
	public $middleware = [];

	/*
	* Route Namespace
	*/
	public $namespace = '';

	/*
	* Route Method
	*/
	public $method = '';

	public function __construct($route, $callback, $method, $middlewares, $namespace){
		$this->route      = $route;
		$this->callback   = $callback;
		$this->method     = $method;
		$this->middleware = $middlewares;
		$this->namespace  = $namespace;
	}

	/*
	* the middleware is an array.
	*/
	public function middleware(){
		/*
		* The route may have middlewares added from it's group closure
		* I have to be sure that the new middlewares are appended to the old middlewares
		*/
		$this->middleware = merge_and_get_unique($this->middleware, func_get_args());

		return $this;
	}

	/*
	* Set the name of the route.
	*/
	public function name($name = ''){
		/*
		* Add the name to the current name
		*/
		$this->name = $name;

		return $this;
	}
}