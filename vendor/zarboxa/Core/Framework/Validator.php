<?php

namespace Zarboxa\Core\Framework;

use Exception;
use Zarboxa\Core\Framework\Model;

class Validator {

	/*
	* The user passed data
	*/
	public $data = [];

	/*
	* The user passed rules
	*/
	public $rules = [];

	/*
	* checks weather the validator is failed or not
	*/
	public $fails = false;
	
	/*
	* Error Messages, it's an associative array
	*/
	public $messages = [];

	/*
	* The current active key
	*/
	public $key = null;

	/*
	* The current active value
	*/
	public $value = null;


	public function __call($property, $data){

		$data = [];
		$sub2 = substr($property, 0, 2);
		$sub3 = substr($property, 0, 3);
		$sub6 = substr($property, 0, 6);


		if ($sub2 == 'in') {
			$data     = explode(",", substr($property, 3));
			$property =  $sub2 ;
		}
		if ($sub3 == 'min' || $sub3 == 'max') {
			$data     = substr($property, 4);
			$property =  $sub3 ;
		}
		if ($sub6 == 'unique' || $sub6 == 'exists') {
			$data     = explode(",", substr($property, 7));
			$property =  $sub6 ;
		}

		$check = 'check' . ucfirst($property);

		if (method_exists( $this, $check)) {
			return $this->$check($data);
		}else{
			if ($property != 'required') {
				throw new Exception("This rule : " . $property . " is not exist", 1);
			}
		}
	}

	public function messages(){
		return $this->messages;
	}

	public function fails(){
		return count($this->messages) > 0 ? true : false;
	}

	public static function make($data, $rules){

		$obj = new self;

		$obj->data  = $data;
		$obj->rules = $rules;

		foreach ($rules as $key => $validations) {
			$validations = explode('|', $validations);

			$obj->key = $key ;

			$obj->checkRequired($validations);

			if ($obj->keyExists()){
				$obj->value = $obj->data[ $key ];

				foreach ($validations as $rule) {
					if ( $msg = $obj->$rule() ) {
						if (is_array($msg)) {
							foreach ($msg as $error) {
								$obj->messages[ $key ][] = str_replace(":field", $key, $error);
							}
						}else{
							$obj->messages[ $key ][] = str_replace(":field", $key, $msg);
						}
					}
				}
			}

		}
		return $obj;
	}

	public function keyExists(){
		if (isset($this->data[ $this->key ]) && !empty($this->data[ $this->key ])) {
			return true;
		}
		return false;
	}

	/*
	* Check weather the field is exist or not
	*/
	public function checkRequired($validations = []){
		if (in_array('required', $validations) && !$this->keyExists()) {
			$this->messages[ $this->key ][] = "The " . $this->key . " field is required";
		}
	}

	/*
	* String validation
	*/
	public function checkString(){
		if (!is_string($this->value)) {
			return "The :field field must be a string";
		}
	}

	/*
	* Number validation
	*/
	public function checkNumber(){
		if (!is_numeric($this->value)) {
			return "The :field field must be a number";
		}
	}

	/*
	* Array validation
	*/
	public function checkArray(){
		if (!is_array($this->value)) {
			return "The :field field must be an array";
		}
	}

	/*
	* Email validation
	*/
	public function checkEmail(){
		if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
			return "The :field field must be a valid email";
		}
	}

	/*
	* Min value validation
	*/
	public function checkMin($min){

		$type = gettype($this->value);
		$error = false;

		if ($type == 'array'  && count ($this->value) > $min) { $error = true; }
		if ($type == 'string' && strlen($this->value) < $min && !is_numeric($this->value)) { $error = true; }
		if (is_numeric($this->value) && (int) $this->value < $min) { $error = true; }

		if ($error){ return "The :field field must not lower than " . $min; }
	}

	/*
	* Max value validation
	*/
	public function checkMax($max){

		$type = gettype($this->value);
		$error = false;

		if ($type == 'array'  && count ($this->value) < $max) { $error = true; }
		if ($type == 'string' && strlen($this->value) > $max && !is_numeric($this->value)) { $error = true; }
		if (is_numeric($this->value) && (int) $this->value > $max) { $error = true; }

		if ($error){ return "The :field field must not lower than " . $max; }
	}

	/*
	* In Array validation
	*/
	public function checkIn($data){
		if (!in_array($this->value, $data)) {
			return "This :field field does not have a valid value";
		}
	}

	/*
	* Exists In database validation
	*/
	public function checkExists($data){
		return $this->existsUniqueValidation($data);
	}

	/*
	* Exists In database validation
	*/
	public function checkUnique($data){
		return $this->existsUniqueValidation($data, false);
	}

	public function existsUniqueValidation($data, $exists = true){
		/*
		* this is used to escape a custom id
		*/
		$escape = !empty($data[2]) ? ' AND id <> {$data[2]}' : '' ;

		$f = Model::one("SELECT {$data[1]} FROM {$data[0]} WHERE $data[1]='{$this->value}' {$escape} LIMIT 1");

		$msg = $exists ? "The :field field doesn't match our records" : $this->value . ' has been taken';

		$error = $f ? ( $exists ? false : true ) : ( $exists ? true : false );
		if ($error) { return $msg; }
	}

	/*
	* Confirmed validation
	*/
	public function checkConfirmed(){
		$errors = [];
		if (!isset($this->data[ $this->key . '_confirmation'])) {
			$errors[] = "The :field field must be confirmed";
		}else{
			if (empty($this->data[$this->key . '_confirmation']) && $this->data[$this->key] !== $this->data[$this->key.'_confirmation']) {
				$errors[] = "The :field field confirmation doesn't match";
			}
		}
		return $errors;
	}

}
