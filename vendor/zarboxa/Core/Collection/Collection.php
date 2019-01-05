<?php

namespace Zarboxa\Core\Collection;

class Collection{

	/*
	* This class holds the helper functions of the collect class
	*/

	/*
	* hold the collection data
	*/
	protected $data = [];

	/*
	* create an instance of the collection
	*/
	public static function make($data){

		$collection = new static;

		$collection->data = $data;

		return $collection;
	}

	/*
	* check if the argument is callable or not
	*/
	public function callableArgument($callback){
		if (!is_callable($callback)) {
			throw new Exception("you have to pass a callback function", 1);
		}
	}

	/*
	* When the collection is used as a string
	*/
	public function __toString(){
        return json_encode($this->data);
	}

	/*
	* Get the collection data
	*/
	public function toJson(){
        return $this->data;
	}

	/*
	* Ask if a given array is associative array or indexed
	*/
	function isAssoc(){
	    if (array() === $this->data) return false;
	    return array_keys($this->data) !== range(0, count($this->data) - 1);
	}
}