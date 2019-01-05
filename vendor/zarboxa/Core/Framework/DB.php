<?php

namespace Zarboxa\Core\Framework;

use PDO;
use PDOException;

class DB{

	protected static $con = null;

	public static function con(){
		if (!self::$con) {
			try {
				$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_DATABASE, DB_USERNAME, DB_PASSWORD);
				// set the PDO error mode to exception
				$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$con = $con;
			}catch(PDOException $e){
				echo "Connection failed: " . $e->getMessage();
			}
		}
		return self::$con;
	}

	public static function query($sql){
		try{
			return self::con()->exec($sql);
		}catch(PDOException $e){
		    echo $sql . "<br>" . $e->getMessage();
		}
	}
	
	public static function lastInsertId(){
	    return self::con()->lastInsertId();
	}

	public static function execute($sql, $data=[]){
		try {
			/*
			* "INSERT INTO {$table} (firstname, lastname, email) 
			*  VALUES (:firstname, :lastname, :email)"
			*/
			$stmt = self::con()->prepare($sql);
			foreach ($data as $key => $value) {
				$stmt->bindParam(":{$key}", $$key);
				$$key = $value;
			}
			$stmt->execute();

			return self::lastInsertId();
		}catch(PDOException $e){
			echo $e->getMessage();
		}
	}

	public static function fetchAssoc($sql=''){
		try {
		    $stmt = self::con()->prepare($sql); 
		    $stmt->execute();

		    // set the resulting array to associative
		    $stmt->setFetchMode(PDO::FETCH_ASSOC); 
		    return $stmt->fetchAll();
		} catch (PDOException $e) {
			die("you have an error with : " . $sql . "<br>" . $e->getMessage());
		}
	}

	/*
	try{
	    // begin the transaction
	    $conn->beginTransaction();

	    // our SQL statements
	    $conn->exec("SQL");
	    $conn->exec("SQL");
	    $conn->exec("SQL");

	    // commit the transaction
	    $conn->commit();
	}catch(PDOException $e){
	    $conn->rollback();
	    echo "Error: " . $e->getMessage();
	}
	*/
	public static function beginTransaction(){
	    self::con()->beginTransaction();
	}
	public static function commit(){
	    self::con()->commit();
	}
	public static function rollBack(){
	    self::con()->rollback();
	}
}
