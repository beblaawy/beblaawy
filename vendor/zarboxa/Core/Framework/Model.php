<?php

namespace Zarboxa\Core\Framework;

use Exception;
use Zarboxa\Core\Framework\DB;
use Zarboxa\Core\Framework\QueryBuilder;

class Model extends DB{

	use QueryBuilder;

    /*
	use QueryBuilder {
        QueryBuilderHandler as protected QueryBuilderHandler;
    }
    */

	protected $table = '';

	protected $primaryKey = 'id';

	protected $fillable = [];

	/*
	* Handle if a context property is not accesible or not exist.
	*/
	public function __get($property){
		if (property_exists($this, $property)) {
			return $this->$property;
		}
		return null;
	}

	/*
	* Call the queryMethods functions
	*/
	public function __call($name, $params){
		if (in_array($name, static::$queryMethods)) {
			return $this->QueryBuilderHandler($name, $params);
		}
	}

	/*
	* Handle called un Accessible static methods
	*/
	public static function __callStatic($name, $arguments){
		if (in_array($name, static::$queryMethods)) {
			return (new static)->QueryBuilderHandler($name, $arguments);
		}

		if (strtolower(substr($name, 0, 3)) == 'get') {
			$property = lcfirst(substr($name, 3));
			return (new static)->$property;
		}
		return null;
	}

	public static function getTable(){
		$model = new static;
		if ($model->table) {
			return $model->table;
		}
		$arr = explode('\\', static::class);
		return strtolower(array_pop($arr)) . 's';
	}

	public static function fetch($sql){
	    $res  = self::fetchAssoc($sql);
	    $data = [];
	    foreach($res as $row) { 
	        $obj = new static;
	        foreach ($row as $column => $value) {
	        	$obj->$column = $value;
	        }
	        $data[] = $obj;
	    }
	    return $data;
	}

	public static function all(){
		return static::fetch("SELECT * FROM " . static::getTable());
	}

	public static function one($sql){
		$res = static::fetch($sql);
		return $res ? $res[0] : null;
	}

	public static function find($id){
		$sql  = "SELECT * FROM " . static::getTable();
		$sql .= " WHERE " . static::getPrimaryKey() . "={$id} LIMIT 1";
		return static::one($sql);
	}

	public static function insert($data=[]){
		if (count($data) == 0) {
			throw new Exception("Invalid data passed to " . __FUNCTION__ . " function", 1);
		}
		$sql  = "INSERT INTO " . static::getTable();
		$sql .= " ( " . implode(',', array_keys($data)) .") ";
		$sql .= "VALUES (:" . implode(',:', array_keys($data)) . ")";

		/* It returns the last inserted id */
		$id  = self::execute($sql, $data);

		$obj = new static;
		$primaryKey = $obj->primaryKey;
		$obj->$primaryKey = $id;

		foreach ($data as $key => $value) {
			$obj->$key = $value;
		}
		return $obj;
	}
	
	public static function create($data=[]){
		return static::insert($data);
	}

	public function save(){
		$data = [];
		foreach ($this->fillable as $property){
			if (property_exists($this, $property)) {
				$data[$property] = $this->$property;
			}
		}
		$obj = static::insert($data);
		$primaryKey = $this->primaryKey;
		$this->$primaryKey = $obj->$primaryKey;

		return $this;
	}
}