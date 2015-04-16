<?php

class debug {
	
	private $debugFile = "_debug/debug.txt";

	
	public function __construct($file) {
		
		$this->debugFile = $file;

	}
	
	
	public function __destruct() {
			
			
	}
	
	public function __get($name) {
		return $this->$name;
	}  //----------------------------------------------- End  get function.
	
	public function __set($name, $value) {
		$this->$name=$value;
	}
	
	
	public function header($hdr)
	{
		$headerTxt = "-------- ";
		$headerTxt .= $hdr;
		$headerTxt .= " --------";
		return $headerTxt;		
	}
	
	public function test($testVar)
	{
		$file = fopen($this->debugFile,"w");
		fwrite($file, $testVar);
		fclose($file);
	}
	
	
	
}
