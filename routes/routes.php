<?php

/*
* Here you can define all your routes
*/

use App\User;
use Zarboxa\Core\Routing\Route;
use Zarboxa\Core\Framework\Curl;
use Zarboxa\Core\Framework\Auth;
use Zarboxa\Core\Framework\Request;
use Zarboxa\Core\Framework\Response;
use Zarboxa\Core\Framework\Validator;



Route::get('/', function(){
  $data = [
    ['name' => 'sarah', 'email' => 'sarah@gmail.com', 'age' => 24, 'gender' => 'female'],
    ['name' => 'yara',  'email' => 'yara@gmail.com',  'age' => 14, 'gender' => 'female'],
    ['name' => 'hossam','email' => 'hossam@gmail.com','age' => 95, 'gender' => 'male']
  ];
  return $data;
});


Route::post('/users', function(){
  $user = new User;
  $user->firstname = 'sarah';
  $user->lastname = 'tarek';
  $user->email = 'sarah@gmail.com';
  $user->password = '123456789';
  $user->role = 'user';
  $user->save();
});

/*
Route::get('test', function(){
	echo '<pre>';

	// $test = App\User::where(['firstname'=>'sadfds'],['lastname'=>'sadfds'])->select('first','last')->get();
	$test = App\User::select('firstname','lastname')->limit(4)->offset(10);
	// echo $test->buildFinalQuery();
	return $test->first();

	foreach ($test->get() as $key => $value) {
		// print_r($value);
		echo $value->firstname .'<br>';
	}
	// print_r($test->sqlQuery);
});
*/
Route::group(['prefix' => 'admin', 'middleware'=> ['role:admin']], function(){

	Route::get('users', function(){

		$curl = Curl::make("http://localhost/zarboxa/public/admin/admin/users",['name'=>'sarah'])
					->setMethod("POST")
					->excute();

		return $curl;

		// return view('index')->with([ 'name' => 'hello there', 'users' => App\User::all() ]);

		// $test = App\User::select('firstname','lastname')->limit(6)->offset(10);
		// return $test->get();
	});
	Route::post('admin/users', function(){
		$test = App\User::select('firstname','lastname')->limit(2)->offset(10);
		return $test->get();
	});

	Route::group(['prefix' => 'dash1'], function(){
		Route::get('/users', function(){});

		Route::group(['prefix' => 'dash2'], function(){
			Route::get('users', function(){})->middleware('auth');

			Route::get('/hello', function(){});
		});
	});

	Route::group(['prefix' => 'dash2','namespace' => 'Admin' ], function(){
		Route::get('users', function(){})->middleware('auth');

		Route::get('/hello', function(){});
	});

	Route::get('comments', 'Comments@index');
});

Route::get('/login', function(){
	return 'This is the <b>login</b> page! please login';
});

// Route::all();