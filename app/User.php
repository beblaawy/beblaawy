<?php

namespace App;

use Zarboxa\Core\Framework\Model;

class User extends Model{

	protected $table = 'users';

	protected $fillable = ['firstname','lastname','email','password','usertype'];
}