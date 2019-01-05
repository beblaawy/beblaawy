<?php

namespace Zarboxa\Core\Collection;


/*
* make this ones :
* collapse, 
*/
class Collect extends Collection{

	/*
	* this class holds the actual methods of the collection
	*/

	/*
	* get a value of a key as array
	*/
	public function pluck(){
		$keys = flatten_function_arguments(func_get_args());

		if (count($keys) == 1) {
			$this->each(function($item) use($keys){
				return $item[ $keys[ 0 ] ];
			});
		}elseif (count($keys) > 1) {
			$this->only($keys);
		}

		return $this;
	}

	/*
	* Get only keys from collection
	*/
	public function only(){
		$keys = flatten_function_arguments(func_get_args());

		$this->each(function($item) use($keys){
			$data = [];
			foreach ($item as $key => $value) {
				if (in_array($key, $keys)) {
					$data[ $key ] = $value;
				}
			}
			return $data;
		});

		return $this;
	}

	/*
	* Iterate on the collection
	* apply function on a collection
	*/
	public function each($callback){
		foreach ($this->data as $key => $item) {
			$this->data[ $key ] = $callback($item, $key);
		}
		return $this;
	}

	/*
	* Return all data
	*/
	public function all(){
		return $this->data;
	}

	/*
	* Return all data
	*/
	public function toArray(){
		return $this->all();
	}

	/*
	* Return average of a column
	* alias for avg
	*/
	public function average($key){
		if ($this->isAssoc()) {
			$data = $this->pluck($key)->all();
		}else{
			$data = $this->data;
		}
		$this->data = array_sum($data) / count($data);

		return $this;
	}

	/*
	* Return average of a column
	*/
	public function avg($key){
		return $this->average($key);
	}

	/*
	* Return chunks of an array
	*/
	public function chunk($length){
		$this->data = array_chunk($this->data, $length);

		return $this;
	}

	/*
	* Collapse multidimensional aray to a single array
	*/
	public function collapse(){
		$data = [];
		foreach ($this->data as $value) {
			if (is_array($item)) {
				
			}
		}
	}

	/*
	* Checks if the collection has a given value
	*/
	public function contains($key, $value){
		/*
		if ($this->isAssoc()) {
			$this->data = $this->pluck($key);
		}
		if (in_array($key, $this->data)) {
			return "true";
		}
		return "false";
		*/
	}

}
