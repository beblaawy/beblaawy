<?php

namespace Zarboxa\Core\Framework;

use Exception;

class Request {

	public static $url = null;

	public static $method = null;

	public static $host = null;

	public static $fullUrl = null;

	public static $data = [];


	public function __get($property){
		if (in_array($_REQUEST, $property)) {
			return $_REQUEST[ $property ];
		}
		return null;
	}

	public static function all(){
		return $_REQUEST;
	}

	public static function url(){
		if (!self::$url) {
			$full  = $_SERVER['REQUEST_URI'];
			$query = $_SERVER['QUERY_STRING'];

			$pos   = $query ? strpos($full, $query) : false;

			// $url   = ( $pos && is_numeric($pos) ) ? substr($full, 0, $pos-1) : $full;
			/* You can use strtok to get string before first occurence of ? */
			$url   = strtok($_SERVER["REQUEST_URI"],'?');
			self::$url = substr($url,(strpos($url, PUBLIC_FOLDER)+6));
		}
		return self::$url;
	}

	public static function method(){
		if (!self::$method) {
			self::$method = strtolower($_SERVER['REQUEST_METHOD']);
		}
		return self::$method;
	}

	public static function host(){
		return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
	}

	public static function fullUrl(){
		return self::host() . $_SERVER['REQUEST_URI'];
	}

	public static function baseUrl(){
		$full= self::fullUrl();
		$url = self::url();

		if ($url == '/') {
			return $full;
		}
		$pos = strpos($full, $url);

		return ( $pos && is_numeric($pos) ) ? substr($full, 0, $pos) : $full;
	}

	public static function isPost(){
		return self::method() === 'post' ? true : false ;
	}
	public static function isGet(){
		return self::method() === 'get' ? true : false ;
	}
}
