<?php

namespace Zarboxa\Core\Framework;

use Zarboxa\Core\Framework\Request;

class Redirect {

	public static function to($url, $status = 200){
	    http_response_code($status);
		header("Location: " . Request::baseUrl() . $url);
		exit();
	}

}
