<?php

class push {
	
	private $apnsClass;
	private $debug = false;
	private $debugger;
	private $xmlResponse;	
	
	
	public function __construct($apns, $debug) {		
		$this->debug = $debug;
		$this->apnsClass = $apns;

	}

	public function __destruct() {
		 
		 
	}
	
	public function __get($name) {
		return $this->$name;
	} 
	
	public function __set($name, $value) {
		$this->$name=$value;
	}
		

	public function delegatePush($type, $msg)
	{	
		
	  $tokens;
	  $messages;
	  			
	  if($type == "flush") {
	  	
	  	$msg = "";
	  	$tokens = $this->apnsClass->retrieveTokens();
	  	$messages = $this->apnsClass->queuedMessages();
	  	
	  	if($tokens != 0)   // check to make sure they're are tokens
	  	{
	  		if($messages != 0)  // make sure they're are messages in queue
	  		{		  				  				  			
	  			$testArray = array();

	  			// format each message, and stuff into temp array.
	  			foreach($messages as $message)
	  			{
	  				$pay['aps'] = array('alert' => $message, 'badge' => 1, 'sound' => 'default');
	  				array_push($testArray, $pay);
	  			}

	  			// call the flush method
				$this->prepareMessageList($tokens, $testArray, false);
								
				$this->xmlResponse = $this->xml("Processing your request...");				
				echo $this->xmlResponse;
				exit();
	  				  				  							  					  						  				  						  					  				  				  	
	  		 } else if($messages == 0) {  // No messages in queue
	  		 	
	  		 	$this->xmlResponse = $this->xml("There are no messages in the Queue.");
	  		 	echo $this->xmlResponse;
				exit();
	  		}  
	  
	    } else if($tokens == 0) {    // No devices to send to
	     	
			$this->xmlResponse = $this->xml("There are no registered devices to send to.");	
  		 	echo $this->xmlResponse;
			exit();
	}
	    
	  // If you just want to store a message for later.
	  } elseif($type == "store") {
	  	
	  	$storeCheck = $this->apnsClass->storeMessage($type, $msg);
	  	
		  	if($storeCheck == 1) {
		  				  	
			  	$this->xmlResponse = $this->xml("Your message is in the Queue.");
	  		 	echo $this->xmlResponse;
				exit();
		  	
		  	} else if($storeCheck == 0) {

			  	$this->xmlResponse = $this->xml("Message could not be stored.  Perhaps it already exists.");
	  		 	echo $this->xmlResponse;
				exit();
	  	    	
	  	    } else if ($storeCheck == 2) {

		  	    $this->xmlResponse = $this->xml("The database could not be connected to at this time. \n
		  	    		  				Please try again later.");
	  		 	echo $this->xmlResponse;
				exit();	    	    	
	  	    }

	  // Pushing a single message only. Which will be stored as sent.
	  } elseif($type == "store_push" ){
	  	
	  	$storeCheck = $this->apnsClass->storeMessage($type, $msg);
	  	 
	  	if($storeCheck == 1)
	  	{
	  		
	  		$tempArray  = array();
	  		$payload['aps'] = array('alert' => $msg, 'badge' => 1, 'sound' => 'default');	
	  		array_push($tempArray, $payload);  			  		 
			$tokens = $this->apnsClass->retrieveTokens();

			if ($tokens > 0)
			{				
				// If there are tokens, then call pushMessage().
				// This function handles the actual push after the XML has been returned.

				$this->prepareMessageList($tokens, $tempArray, true);
				
				$this->xmlResponse = $this->xml("Processing your request...");
	  		 	echo $this->xmlResponse;
				exit();
									
			} else {		

				$this->xmlResponse = $this->xml("There are no tokens in the database to send this message to.");
				$this->xmlResponse .=  '</response>';
	  		 	echo $this->xmlResponse;
				exit();
			}
				
		} else if($storeCheck == 0) {

			$this->xmlResponse = $this->xml("Message could not be stored.  Perhaps it already exists.");
  		 	echo $this->xmlResponse;
			exit();
			  	    	
  	    } else if ($storeCheck == 2) {
  	    	
  	    	$this->xmlResponse = $this->xml("The database could not be connected to at this time. \n Please try again later.");
  		 	echo $this->xmlResponse;
			exit();
  	    }	 	
	  	} // End IF($type == StorePush)
	}  // End DelegatePush() Function
	
	private function prepareMessageList($tokenArray, $payloadArray, $pushOnly)
	{
     
     $path = ($this->debug) ? '_debug/samplePush.txt' : '_assets/tempload.txt';
     $file = fopen($path, "w");
		
				// Begin flush to APNS server.
				foreach($tokenArray as $token)
				{
					foreach($payloadArray as $message)
					{
						$tempLoad = json_encode($message);
						
						if($this->debug) {
							
							 fwrite($file, 0 . 0 . 32 . $token . 0 .
							 strlen($tempLoad) . $tempLoad . "\n");												
						} else {					
											
							fwrite($file, chr(0) . pack("n",32) .
							pack('H*', str_replace(' ', '', $token)) .
							pack("n", strlen($tempLoad)) . $tempLoad . "\n");																				
						}
					}
			  }	

			 	if(!$this->debug && !$pushOnly)
			 	{
					$this->apnsClass->unQueue();
			   }
	           fclose($file);
	}
	

public function finalPush()
{
	$handle = fopen("_assets/tempload.txt", "r");
	$payload = array();
	$SSLReturnTxt = "";
	$errorText = '';
	
	if(!$this->debug)
	{
		if ($handle) {
				
			while (($line = fgets($handle)) !== false)
			{
				array_push($payload, $line);
			}
				
			fclose($handle);
			
			try 
			{
				$SSLReturnTxt = "Your messages have been pushed to the APNS Servers.";
				$sslConnection = $this->apnsClass->openStream();

				foreach($payload as $message)
				{
				  fwrite($sslConnection, $message);
				}					 					 
				//  socket_close($apns);
				  $this->apnsClass->closeStream($sslConnection);

			} catch (Exception $e) {
				
				$message = "Error contacting the server \n";
				$message .= "PHP Error: " . $e . "\n";			
				getDebug::writeMessage($message);
				$SSLReturnTxt = "Trouble contacting the APNS servers.  Please consult the debug file.";
								
			} finally {				
				echo $this->xml($SSLReturnTxt);
				exit();				
			}

		} else {
			echo $this->xml("The payload file could not be opened.");
			exit();
		}
	} else {
		echo $this->xml("Test Payload has been Stored.");
		exit();
	}
}

	private function xml($message)
	{
		$xmlResponse = header('Content-Type: text/xml');
		$xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$xmlResponse .=  '<response>';
		$xmlResponse .=  $message;
		$xmlResponse .=  '</response>';	
		
		return $xmlResponse;
	}
	
	
}  // End Push Class.