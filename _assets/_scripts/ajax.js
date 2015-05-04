/**
 * 
 *   This script handles the AJAX call/response to msgController.php
 *    
 */

// Initialize Variable

var xmlHttp;
var pushXML;

	function initializeVariables() {
		$responseText = document.getElementById("responseText");
		$flushBtn = document.getElementById("flushMessage");
		$storeBtn = document.getElementById("storeMessage");
		$comboBtn = document.getElementById("storePush");
		$messageText = document.getElementById("messageText");
		xmlHttp = createXmlHttpRequestObject();
		pushXML = createXmlHttpRequestObject();
	}

	function startMessage($type) {

		if($type == "flush")
		{
			delegateRequest($type, null);
		} else if ($type == "store") {
			  if($messageText.value == "") {
					$responseText.innerHTML = "Please fill out the message box first.";	
 			  } else {
 				 delegateRequest($type, $messageText.value);
 			  }
		}  else if ($type == "store_push") {
			  if($messageText.value == "") {
					$responseText.innerHTML = "Please fill out the message box first.";	
 			  } else {
 				 delegateRequest($type, $messageText.value);
 			  }
		}
		
	}


	function delegateRequest($type, $msg)
	{
		if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
		{
			$msg = encodeURIComponent($msg);
			xmlHttp.open("GET", "/iOS_push/apns.php?type=" + $type + "&msg=" + $msg, true);
			xmlHttp.onreadystatechange = handleDelegate;
			xmlHttp.send(null);
		}
		else
			setTimeout('process()', 1000);
	}


	function handleDelegate()
	{
		if (xmlHttp.readyState == 4)
		{
			if (xmlHttp.status == 200)
			{
				xmlResponse = xmlHttp.responseXML;
				xmlDocumentElement = xmlResponse.documentElement;
				response = xmlDocumentElement.firstChild.data;
		
				if(response === "Processing your request...") {
		
					$responseText.innerHTML =
					'<i>' + response + '</i>';
					
					 process_push();
					
				} else {
					
					$responseText.innerHTML =
						'<i>' + response + '</i>';
				}
			} else {
			alert("There was a problem accessing the server: " + xmlHttp.statusText);
		    }
	    }
	 }


	function createXmlHttpRequestObject()
	{
		var xmlHttp;

		if(window.ActiveXObject)
		{
			try
			{
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				xmlHttp = false;
			}
		}
		else
		{
			try
			{
				xmlHttp = new XMLHttpRequest();
			}
			catch (e)
			{
				xmlHttp = false;
			}
		}
			if (!xmlHttp)
				alert("Error creating the XMLHttpRequest object.");
			else
				return xmlHttp;	
	}
	
	
	function process_push()
	{
		if (pushXML.readyState == 4 || pushXML.readyState == 0)
		{
			$pushCode = "49 5c a4 f7 78 a2 4f d4 99 a4";
			
			pushXML.open("GET", "/iOS_push/apns.php?pushCode=" + $pushCode, true);
			pushXML.onreadystatechange = handle_push;
			pushXML.send(null);
			
		} else {
       setTimeout('process_push()', 1000);
		}				
	}
	
	
	function handle_push()
	{
		if (pushXML.readyState == 4)
		{
			if (pushXML.status == 200)
			{
				xmlResponse = pushXML.responseXML;
				xmlDocumentElement = xmlResponse.documentElement;
				response = xmlDocumentElement.firstChild.data;
		
				$responseText.innerHTML = response;

			} else {
			alert("There was a problem accessing the server: " + xmlHttp.statusText);
		    }
	    }
	}
	
	
	

	
	window.onload = function() { 
	initializeVariables();
	};









