<?php

function zarboxaAutoCalledClass($class){
	$path  = '../';
	$class = str_replace('\\', '/', $class);

	if (strtolower(substr($class, 0, 3)) == APP_FOLDER) {
		$path .= lcfirst($class);
	}
	if (strtolower(substr($class, 0, 7)) == 'zarboxa') {
		$path .= 'vendor/' . lcfirst($class);
	}
	$path .= '.php';

	if (!file_exists($path)) {
		throw new Exception("the class : {$class} is not exist", 1);
	}
  require_once $path;
}

/* ------------ folder pathes ------------ */
function base_path(string $path=''){
	return ROOT_PATH . $path;
}
function app_path(string $path=''){
	return APP_PATH . $path;
}
function public_path(string $path=''){
	return PUBLIC_PATH . $path;
}
/* --------------------------------------- */

function flatten_function_arguments(array $args = []){
	$data = [];
	foreach ($args as $arg) {
		if (is_array($arg)) {
			$data = array_merge($data, $arg);
		}else{
			$data[] = $arg;
		}
	}
	return $data;
}
function is_assoc(array $arr){
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}
function make_array_from_array_or_string($params){
	if (is_string($params)) {
		return [$params];
	}
	if (is_array($params)) {
		return $params;
	}
	return $params;
}
function merge_and_get_unique(){
	return array_values(array_unique(flatten_function_arguments(func_get_args())));
}
function stripe_the_last_slash($str){
	if ($str != '/' && substr($str, -1, 1) == '/') {
		$str = substr($str, 0, -1);
	}
	return $str;
}

function rand_str($length = 20){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function env($target, $default=null){
	if (isset(ENV_DATA[$target])) {
		return ENV_DATA[$target];
	}
	return $default;
}


function view(String $file, array $data = []){
	return Zarboxa\Core\Views\View::make($file, $data);
}

/*
* return instance on collection
*/
function collect($data){
	return Zarboxa\Core\Collection\Collect::make($data);
}