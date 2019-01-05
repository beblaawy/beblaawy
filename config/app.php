<?php

$data = [
	/*
	* Feel free to change any constant value
	* Kindly don't change any key name!
	* Your application depends on this parameters
	* Be sure that you assign these values correclty
	*/

	/*
	* Define the app folder name, where you put all your Model classes
	* Theses classes are used to connect with the database, like ( User, Post, Item .. etc )
	*/
	'APP_FOLDER' => 'app',

	/*
	* Define the Controllers folder name, where you put all your controller classes
	*/
	'CONTROLLER_FOLDER' => 'app/Http/Controllers',

	/*
	* Define the public folder name, where you put all accessible files
	*/
	'PUBLIC_FOLDER' => 'public',

  /*
  * Define the views folder name, where you put all views files
  */
  'VIEW_FOLDER' => 'views',

	/*
	* Define the vendor folder name, where you put all vendor files
	*/
	'VENDOR_FOLDER' => 'vendor',

	/*
	* This key is used with the middleware auth.
	* If the user is not logged in! Then the request will be redirect to this route
	*/
	'REDIRECT_ROUTE_IF_NOT_AUTH' => '/login',

	/*
	* Database Parameters
	*/
	'DB_HOST'     => env('DB_HOST', 'localhost'),
	'DB_USERNAME' => env('DB_USERNAME', 'root'),
	'DB_PASSWORD' => env('DB_PASSWORD', 's2ur5orm'),
	'DB_DATABASE' => env('DB_DATABASE', 'first'),
];
