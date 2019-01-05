<?php

namespace Zarboxa\Core\Framework;

use Exception;

class Response {

	public static function json($data, $status = 200){

	    http_response_code($status);
		header("Content-Type: application/json;charset=utf-8");
		return json_encode($data);

	}
}