<?php

namespace Zarboxa\Core\Framework;

trait QueryBuilder {

	/*
	* These are the methods the QueryBuilder handles.
	*/
	protected static $queryMethods = ['select', 'where', 'get', 'first', 'limit', 'offset','orderBy'];

	/*
	* SQL Query builder
	*/
	protected $sqlBuilder = [
		"WHERE"  => [],
		"SELECT" => [],
	];

	/*
	* This is holder for the called functions to be used for build the Sql Query
	* I use this function to call only the called function for the query
	*/
	protected $calledBuilderFunctions = [];

	/*
	* This is the functions arrangements, to handle which function should be called first
	*/
	protected $queryFunctionsArrangement = ['where','limit','offset','orderBy'];

	/*
	* Al functions are handled in QueryBuilderHandler function, and it returns $this
	* when I call get, first or any fetched function i wanna return the fetched data
	* so this switch between return the $this object or the fetched data
	*/
	protected $fetchedMethods = ['get', 'first'];

	/*
	* Actual query which will be sent to the database
	*/
	protected $sqlQuery = "";

	public function QueryBuilderHandler($name, $params){

		if (in_array($name, $this->fetchedMethods)) {
			$this->buildFinalQuery();
		}else{
			$this->calledBuilderFunctions[] = $name; 
		}

		$data = call_user_func_array([$this, 'handle' . ucfirst($name)], $params);

		return in_array($name, $this->fetchedMethods) ? $data : $this;
	}

	/* Push the user sent data to sqlBuilder Array */
	protected function handleSelect(){
		$this->sqlBuilder["SELECT"] = flatten_function_arguments(func_get_args()); 
	}
	protected function handleWhere(){
		$this->sqlBuilder["WHERE"][] = flatten_function_arguments(func_get_args()); 
	}
	protected function handleLimit($limit){
		$this->sqlBuilder["LIMIT"] = $limit; 
	}
	protected function handleOffset($offset){
		$this->sqlBuilder["OFFSET"] = $offset; 
	}
	protected function handleOrderBy($column, $by = "ASC"){
		$this->sqlBuilder["ORDER BY"]  = $column; 
		$this->sqlBuilder["ORDER WAY"] = $by; 
	}
	/* -------------------------------------------- */

	/*
	* Handle Select stuff
	*/
	protected function sqlSelect(){
		/*
		* use array_intersect to make sure that :
		* the user slected columns are exists in the fillable array
		*/
		$select = array_intersect($this->fillable, $this->sqlBuilder["SELECT"]);
		
		$this->sqlQuery .= $select ? implode(",", $select) : "*";
	}

	/*
	* Handle From Table stuff
	*/
	protected function sqlTable(){
		$this->sqlQuery .= " FROM " . $this->getTable();
	}

	/*
	* Handle Where stuff
	*/
	protected function sqlWhere(){
		/*
		* use array_intersect to make sure that :
		* the where columns are exists in the fillable array
		*/
		foreach ($this->sqlBuilder["WHERE"] as $row) {
			$columns = array_intersect($this->fillable, array_keys($row));
			if ($columns) {
				$this->sqlQuery .= " WHERE (";
				foreach ($columns as $i => $column) {
					$this->sqlQuery .= $column . " = '" . $row[$column] ."'";
					if ($i < (count($columns) - 1)) {
						$this->sqlQuery .= " AND ";
					}
				}
				$this->sqlQuery .= ")";
			}
		}
	}

	/*
	* Handle LIMIT stuff
	*/
	protected function sqlLimit(){
		$this->sqlQuery .= " LIMIT " . $this->sqlBuilder["LIMIT"];
	}

	/*
	* Handle OFFSET stuff
	*/
	protected function sqlOffset(){
		$this->sqlQuery .= " OFFSET " . $this->sqlBuilder["OFFSET"];
	}

	/*
	* Handle ORDER BY stuff
	*/
	protected function sqlOrderBy(){
		$this->sqlQuery .= " ORDER BY " . $this->sqlBuilder["ORDER BY"] . $this->sqlBuilder["ORDER WAY"];
	}

	protected function buildFinalQuery(){
		$this->sqlQuery = "SELECT ";
		$this->sqlSelect();
		$this->sqlTable();

		foreach ($this->queryFunctionsArrangement as $func) {
			if (in_array($func, $this->calledBuilderFunctions)) {
				$this->{'sql'.$func}();
			}
		}
		return $this->sqlQuery;
	}

	/*
	* fetch the sqlQuery
	* it returns an array of objects
	*/
	protected function handleGet(){
		return $this->fetch( $this->sqlQuery );
	}

	/*
	* fetch the sqlQuery
	* it returns an object of the Model
	*/
	protected function handleFirst(){
		return $this->one( $this->sqlQuery );
	}
}