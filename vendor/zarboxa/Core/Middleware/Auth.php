<?php

namespace Zarboxa\Core\MiddleWare;

use Zarboxa\Core\Framework\Auth as AuthUser;
use Zarboxa\Core\Framework\Redirect;

class Auth {

	public function handle(){
	    if (!AuthUser::check()) {
	    	Redirect::to(REDIRECT_ROUTE_IF_NOT_AUTH, 403);
	    }
	}
}
