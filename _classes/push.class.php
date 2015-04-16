<?php

class push {
	
	private $apnsHost = 'gateway.sandbox.push.apple.com';   // take out sandBox to go live
	private $apnsPort = 2195;
	private $apnsCert = '_assets/_certs/apns-dev.pem';   // store apns-dev.pem file in this location
	private $apnsPass = "";
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
	  			$this->xmlResponse = header('Content-Type: text/xml');
	  			$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	  			$this->xmlResponse .=  '<response>';
	  			$this->xmlResponse .=  'All queued messages have been pushed.';
	  			$this->xmlResponse .=  '</response>';

	  			$testArray = array();

	  			// format each message, and stuff into temp array.
	  			foreach($messages as $message)
	  			{
	  				$pay['aps'] = array('alert' => $message, 'badge' => 1, 'sound' => 'default');
	  				array_push($testArray, $pay);
	  			}

	  			// call the flush method, which sends all queued messages to 
	  			// each registered with the app.
				$this->flushQueuedMessages($tokens, $testArray);
				return $this->xmlResponse;
	  				  				  							  					  						  				  						  					  				  				  	
	  		 } else if($messages == 0) {  // No messages in queue

	  		 	$this->xmlResponse = header('Content-Type: text/xml');
	  		 	$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	  		 	$this->xmlResponse .=  '<response>';
	  		 	$this->xmlResponse .=  "There are no messages in the Queue.";
	  		 	$this->xmlResponse .=  '</response>';
	  		 	return $this->xmlResponse;
	  		}  
	  
	    } else if($tokens == 0) {    // No devices to send to
	     	
	     	$this->xmlResponse = header('Content-Type: text/xml');
	     	$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	     	$this->xmlResponse .=  '<response>';
	     	$this->xmlResponse .=  "There are no registered devices to send to";
	     	$this->xmlResponse .=  '</response>';	
	     	return $this->xmlResponse;
	    }

	  // If you just want to store a message for later.
	  } elseif($type == "store") {
	  	
	  	$storeCheck = $this->apnsClass->storeMessage($type, $msg);
	  	
		  	if($storeCheck == 1) {
		  		
		  	$this->xmlResponse = header('Content-Type: text/xml');
		  	$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		  	$this->xmlResponse .=  '<response>';
		  	$this->xmlResponse .=  "Your message has been stored, and is in the queue.";
		  	$this->xmlResponse .=  '</response>';
		  	return $this->xmlResponse;
		  	
		  	} else if($storeCheck == 0) {

	  		$this->xmlResponse = header('Content-Type: text/xml');
	  		$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	  		$this->xmlResponse .=  '<response>';
	  		$this->xmlResponse .=  "This message could not be stored. Perhaps it already exists.";
	  		$this->xmlResponse .=  '</response>';
	  		return $this->xmlResponse;

	  	    	
	  	    } else if ($storeCheck == 2) {
	  	    	
  	    	$this->xmlResponse = header('Content-Type: text/xml');
  	    	$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
  	    	$this->xmlResponse .=  '<response>';
  	    	$this->xmlResponse .=  "The database could not be connected to at this time. \n
	  	    		  				Please try again later.";
  	    	$this->xmlResponse .=  '</response>';
  	    	return $this->xmlResponse;
  	    	    	
	  	    }

	  // Pushing a single message only. Which will be stored as sent.
	  } elseif($type == "store_push" ){
	  	
	  	$storeCheck = $this->apnsClass->storeMessage($type, $msg);
	  	 
	  	if($storeCheck == 1)
	  	{
	  		
	  		$payload['aps'] = array('alert' => $msg, 'badge' => 1, 'sound' => 'default');
	  		$payload = json_encode($payload);
	  			  		  	
  	    	$this->xmlResponse = header('Content-Type: text/xml');
  	    	$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
  	    	$this->xmlResponse .=  '<response>';
			
			$tokens = $this->apnsClass->retrieveTokens();

			if ($tokens > 0)
			{
				$this->xmlResponse .=  "Your message has been pushed.";
				$this->xmlResponse .=  '</response>';
				
				// If there are tokesn, then call pushMessage().
				// This function handles the actual push after the XML has been returned.

				$this->pushMessage($tokens, $payload);
				return $this->xmlResponse;
									
			} else {				
				$this->xmlResponse .= "There are no tokens in the database to send this message to.";
				$this->xmlResponse .=  '</response>';
				return $this->xmlResponse;
			}

				
		} else if($storeCheck == 0) {

	  		$this->xmlResponse = header('Content-Type: text/xml');
	  		$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	  		$this->xmlResponse .=  '<response>';
	  		$this->xmlResponse .=  "This message could not be stored. Perhaps it already exists.";
	  		$this->xmlResponse .=  '</response>';
	  		return $this->xmlResponse;
  	    	
  	    } else if ($storeCheck == 2) {
  	    	
  	    	$this->xmlResponse = header('Content-Type: text/xml');
  	    	$this->xmlResponse .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
  	    	$this->xmlResponse .=  '<response>';
  	    	$this->xmlResponse .=  "The database could not be connected to at this time. \n
	  	    		  				Please try again later.";
  	    	$this->xmlResponse .=  '</response>';
  	    	return $this->xmlResponse;
  	    }	
			
			
  	    	
	  	} // End IF($type == StorePush)
	}  // End DelegatePush() Function
	
	
	
	private function pushMessage($tokenArray, $payload)
	{
		// Set variables based on Debug Mode
		if($this->debug){
			
		$file = fopen("_debug/samplePush.txt","w");
	
		
		} else if(!$this->debug) {
			
			$streamContext = stream_context_create();
			stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->apnsCert);
			stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->apnsPass);
			$apns = stream_socket_client('ssl://' . $this->apnsHost . ':' .
					 $this->apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
		}
		
		
		// Begin message push to APNS server.
		foreach ($tokenArray as $token)
		{
			if($this->debug)
			{			
				fwrite($file, 0 . 0 . 32 . $token .
				 0 . strlen($payload) . $payload . "\n");
				
			} else if(!$this->debug) {
				
				// begin push
				
				$apnsMessage = chr(0) . pack("n",32) .
				pack('H*', str_replace(' ', '', $token)) .
				pack("n", strlen($payload)) . $payload;
				
				fwrite($apns, $apnsMessage);				
			
			}
		}
		
		// Close connection based on debug mode.
		if($this->debug){
			fclose($file);
		} else if(!$this->debug) {
			socket_close($apns);
			fclose($apns);
		}
			
	}

	
	
	private function flushQueuedMessages($tokenArray, $payloadArray)
	{
		
		// Set variables for live or debug mode.
		if($this->debug){
			
		$file = fopen("_debug/samplePush.txt","w");
		
		} else if(!$this->debug) {
			
			$streamContext = stream_context_create();
			stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->apnsCert);
			stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->apnsPass);
			$apns = stream_socket_client('ssl://' . $this->apnsHost . ':' .
					 $this->apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
		}
		
		// Begin flush to APNS server.
		foreach($tokenArray as $token)
		{
			foreach($payloadArray as $message)
			{
				$tempLoad = json_encode($message);
		
				if($this->debug) {
					
					// debug just writes plain test to _debug/samplePush.txt
					fwrite($file, 0 . 0 . 32 . $token . 0 .
					 strlen($tempLoad) . $tempLoad . "\n");							
				} else {					
					
					// flushes all binary payloads to the APSN server in succession.
					$apnsMessage = chr(0) . pack("n",32) .
					pack('H*', str_replace(' ', '', $token)) .
					pack("n", strlen($payload)) . $payload;
					
					fwrite($apns, $apnsMessage);
										
				}
			}
		}	

		// Close connection based on debug mode.
		if($this->debug){
			fclose($file);
		} else if(!$this->debug) {
			// Unqueue the messages we just sent.
			$this->apnsClass->unQueue();
			// Close Connection Stream.
			socket_close($apns);
			fclose($apns);
		}		
	}
	
	
}  // End Push Class.