<?php

namespace Zarboxa\Core\Framework;

use App\User;

class Auth extends User{

	protected static $id      = null;

	protected static $user    = null;

	protected static $checked = false;

	/*
	* Handle user data stuff.
	* handle if the user is logged on or not!
	*/
	public static function init(){
		if (!self::$user && !self::$checked) {
			if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {

				$user = User::find($_SESSION['user_id']);
				if ($user) {
					self::$user    = $user;
					self::$id      = $user->id;
					self::$checked = true;
				}
			}
		}
	}

	/*
	* Get the user data.
	*/
	public static function user(){
		return self::$user;
	}

	/*
	* Get the user id.
	*/
	public static function id(){
		return self::$id;
	}

	/*
	* asks if the user is logged in or not.
	*/
	public static function check(){
		return self::$user ? true : false;
	}

	/*
	* asks if the user is guest or not.
	*/
	public static function guest(){
		return self::$user ? false : true;
	}
}
Auth::init();