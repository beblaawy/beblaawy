<?php
use Zarboxa\Core\Routing\Route;
use Zarboxa\Core\Framework\Zarboxa;


require_once '../vendor/zarboxa/Core/Framework/HelperFunctions.php';
require_once '../vendor/zarboxa/Core/Framework/Zarboxa.php';

/*
* Composer package autoloader
*/
require_once '../vendor/autoload.php';

spl_autoload_register('zarboxaAutoCalledClass');

Zarboxa::init();

require '../routes/routes.php';

/*
* This is too important to return the response to the user
* It checks which current url that user requested to call it's invoked function
*/
Route::returnResponseToUser();