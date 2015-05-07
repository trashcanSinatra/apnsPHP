<?php


class apns 
{
	
	private $db;
	private $tokens;
	private $msg;
	private $messages;
	private $apnsHost = 'gateway.sandbox.push.apple.com';   // take out sandBox to go live
	private $apnsPort = 2195;
	private $apnsCert = '_assets/_certs/apns-dev.pem';   // store apns-dev.pem file in this location
	private $apnsPass = "";
	
	public function __construct($connection) 
	{
		$this->db = $connection;
	}
	
	
	public function __destruct() 
	{
			
			
	}
	
	public function __get($name)
	{
		return $this->$name;
	}  //----------------------------------- End  get function.
	
	public function __set($name, $value) {
		$this->$name=$value;
	}
	
	
	public function storeDeviceToken($token)
	{
		
		$conn = $this->db->connection;
		
		if(!$conn) {
		
			return 2;
		
		} else {
		
			$appQuery = "Select * FROM tokens WHERE device_token = '$token'";
			$appResult = mysqli_query($conn, $appQuery);
		
			if(mysqli_num_rows($appResult) > 0) {
		
				return 0;
		
			} else {
		
				$regQuery = "INSERT INTO tokens (device_token) VALUES ('$token') ";
				$regResult = mysqli_query($conn, $regQuery);
		
				if(!$regResult || mysqli_affected_rows($conn) == 0)
				{
		
					return 0;
		
				} else {
		
					return 1;
		
				}
		
			}
			
			mysqli_free_result($appResult);
			mysqli_free_result($regResult);
			mysqli_close($conn);
		}
		
			
		
	}
	
	
	
	public function storeMessage($type, $msg)
	{
		$conn = $this->db->connection;
		$msg = $this->db->mysql_text_prep($msg);
		
		$status = "Sent";
		
		if($type == 'store') {
			
			$status = "Queued";
			
		}
		
		if(!$conn) {
		
			return 2;
		
		} else {
		
			$regQuery = "INSERT INTO messages (`message`, `status`) VALUES ('$msg', '$status')";
			$regResult = mysqli_query($conn, $regQuery);
	
			if(!$regResult || mysqli_affected_rows($conn) == 0)
			{
	
				return 0;
	
			} else {
	
				return 1;
	
		    }	
	    }
	}
	
	
	
	
	public function retrieveTokens()
	{
		
		$this->tokens = array();
		$query = "Select * FROM tokens";
		$result = mysqli_query($this->db->connection, $query);
		
		while($id = mysqli_fetch_assoc($result))
		{
			if($id["device_token"] != "")
			{
				array_push($this->tokens,  $id["device_token"]);
			}
		}
		
		if (count($this->tokens)  == 0) {			
			return 0;			
		} else {
		return $this->tokens;
		}
			
		mysqli_free_result($result);
		mysqli_close($db->connection);
		
	}
	
	
	public function queuedMessages()
	{
		$queued = "Queued";
		$this->messages = array();
		$query = "Select `message` FROM messages WHERE `status` = '$queued' ";
		$result = mysqli_query($this->db->connection, $query);
		
		while($msg = mysqli_fetch_assoc($result))
		{
			if($msg["message"] != "")
			{
				array_push($this->messages,  $msg["message"]);
			}
		}
				
		if (count($this->messages)  == 0) {
			return 0;
		} else {
			return $this->messages;
		}
			
		mysqli_free_result($result);
		mysqli_close($db->connection);
				
	}
	
	
	public function unQueue() 
	{
		$sent = "Sent";
		$queued = "Queued";
		$query = "Update `messages` SET `status` = '$sent' WHERE `status` = '$queued' ";
		$result = mysqli_query($this->db->connection, $query);
		
		if(!$result || mysqli_affected_rows($this->db->connection) == 0)
		{
			return 0;
			
		} else {
			
			return 1;
			
		}
		
		mysqli_free_result($result);
		mysqli_close($this->db->connection);		
		
	}

	public function openStream() 
	{
				$streamContext = stream_context_create();
				stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->apnsCert);
				stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->apnsPass);
				$apns = stream_socket_client('ssl://' . $this->apnsHost . ':' . $this->apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);

				if(!$apns && $error >= 0)
				{
					Throw new Exception("Trouble contacting the APNS servers.  Please consult the debug file.");
				} else
				{
					return $apns;
				}
	}


	public function closeStream($stream)
	{
		fclose($stream);
	}
	

	
}