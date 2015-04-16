<?php

class getDebug {
	
	public static $activeBug;
	public static $file = "_debug/debug.txt";
	
	
	
	static function returnBug()
	{
		if(self:: $activeBug == null)
		{
			
		    self:: $activeBug = new debug(self::$file);
		}
			
	       return self:: $activeBug;
	}
	
	
	
	static function writeMessage($message)
	{		
		self::returnBug()->test($message);			
	}
	
}
	
