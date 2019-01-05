<?php

namespace Zarboxa\Core\Framework;

use Exception;

class Curl{

	/*
	* I save the ini value in this property
	*/
	protected $ini = null;

	/*
	* Parameters sent with the curl
	* This is json data
	*/
	protected $data = '';

	/*
	* Curl Method
	*/
	protected $method = "GET";

	/*
	* Returned Output
	*/
	protected $output = "";

	/*
	* When tring to use the object as a string
	*/
	/*
	public function __toString(){
	}
	*/
	public static function make(String $url, array $data = []){
		try {
			return self::ini($url, $data);
		} catch(Exception $e) {

		    trigger_error(sprintf(
		        'Curl failed with error #%d: %s',
		        $e->getCode(), $e->getMessage()),
		        E_USER_ERROR);
		}
	}

	public static function ini(String $url, array $data = []){
		$ch = curl_init();

	    if (FALSE === $ch){
	        throw new Exception('Failed to initialize Curl');
	    }
	    $curl = new self;
	    $curl->ini = $ch;

        $curl->setUrl($url);
        $curl->setMethod("GET");

        $curl->setData($data);
        $curl->setHeader('Content-Type: application/json');

        $curl->setTransfer(true)
		     ->setFollowLocation(1)
		     ->setTimeOut(5)
		     ->setConnectTimeOut(5);
	        
	    return $curl;
	}

	public function setUrl(String $url){
        curl_setopt($this->ini, CURLOPT_URL, $url);
        return $this;
	}

	public function setMethod(String $method = "GET"){
		$this->method = $method;
        curl_setopt($this->ini, CURLOPT_CUSTOMREQUEST, $method);
        return $this;
	}

	public function setData(array $data = []){
	    $this->data = json_encode($data);
        curl_setopt($this->ini, CURLOPT_POSTFIELDS, $this->data);
        return $this;
	}

	public function setTransfer($transfer = true){
		/*
		* Make it so the data coming back is put into a string
		*/
		curl_setopt($this->ini, CURLOPT_RETURNTRANSFER, $transfer);
        return $this;
	}

	public function setFollowLocation($value = 1){
		curl_setopt($this->ini, CURLOPT_FOLLOWLOCATION, $value);
        return $this;
	}

	public function setTimeOut($time){
        curl_setopt($this->ini, CURLOPT_TIMEOUT, $time);
        return $this;
	}

	public function setConnectTimeOut($time){
        curl_setopt($this->ini, CURLOPT_CONNECTTIMEOUT, $time);
        return $this;
	}

	public function setHeader($headers = []){
        curl_setopt(
        	$this->ini,
        	CURLOPT_HTTPHEADER,
        	array_merge( flatten_function_arguments(func_get_args()), ['Content-Length: ' . strlen($this->data)])
        );
        return $this;
	}

	public function getCode(){
        return curl_getinfo($this->ini, CURLINFO_HTTP_CODE); 
	}

	public function getInfo(){
        return curl_getinfo($this->ini); 
	}

	public function close(){
        curl_close($this->ini);
        return $this;
	}

	public function toJson(){
        return json_decode($this->output);
	}

	public function excute(){
        $this->output = curl_exec($this->ini);
	    if (FALSE === $this->output){
	        throw new Exception(curl_error($this->ini), curl_errno($this->ini));
	    }
	    /*
	    * Should i close curl ?
	    */
        // $this->close();

	    return $this->toJson();
	}
}
