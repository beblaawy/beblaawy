<?php

namespace App\Http;

use Zarboxa\Core\Kernel as BaseKernel; 

class Kernel extends BaseKernel{

	protected $middlewares = [
		/*
		* Add your defined middlwares right here as a key => value
		* The key is the middleware alias where you use it in your Route
		* Example : Route::get('/test', function(){})->middleware('admin');
		* Then your middleware key is "admin"
		* The value is the class name that you created to handle your middleware stuff
		* "admin" => "App\Http\Middleware\Admin"
		*/
		"role" => "App\Http\Middleware\Role",
	];
}