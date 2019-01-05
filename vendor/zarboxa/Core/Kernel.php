<?php

namespace Zarboxa\Core;

use Exception;

class Kernel{

	/*
	* This is the middlewares added by the developer.
	*/
	protected $middlewares = [];

	/*
	* This is the Core middlewares which Zarboxa added.
	* This middlwares name could not be overridden.
	*/
	protected $baseMiddlewares = [
		'auth' => 'Zarboxa\Core\Middleware\Auth',
	];

	public function handle(array $middlewares){
		/*
		* Override the baseMiddlewares with the developer middlwares
		* This limit the developer to override the Core middlewares
		*/
		foreach ($this->baseMiddlewares as $name => $path) {
			$this->middlewares[ $name ] = $path;
		}

		/*
		* Call the middlewares
		*/
		foreach ($middlewares as $middleware) {
			$params = [];
			$data = explode(':', $middleware);

			/*
			* Middleware name is the first index
			*/
			$name = $data[0];

			if (isset($data[1])) {
				/*
				* Explode what comes after : with ( , ) delimeter to pass many values
				*/
				$params = explode(',', $data[1]);
			}

			if (!array_key_exists($name, $this->middlewares)) {
				throw new Exception("You did not add your : {$name} middleware to the Kernel middlewares", 1);
			}
			$class = $this->middlewares[ $name ];
			$class = new $class;
			call_user_func_array([$class, "handle"], $params);
		}
	}
}