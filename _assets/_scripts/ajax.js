/**
 * 
 *   This script handles the AJAX call/response to msgController.php
 *    
 */

// Initialize Variable

var xmlHttp;

	function initializeVariables() {
		$responseText = document.getElementById("responseText");
		$flushBtn = document.getElementById("flushMessage");
		$storeBtn = document.getElementById("storeMessage");
		$comboBtn = document.getElementById("storePush");
		$messageText = document.getElementById("messageText");
		xmlHttp = createXmlHttpRequestObject();
	}

	function startMessage($type) {

		if($type == "flush") {
			processAJAX($type, null);
		} else if ($type == "store") {
			  if($messageText.value == "") {
					$responseText.innerHTML = "Please fill out the message box first.";	
 			  } else {
 			  		processAJAX($type, $messageText.value);
 			  }
		}  else if ($type == "store_push") {
			  if($messageText.value == "") {
					$responseText.innerHTML = "Please fill out the message box first.";	
 			  } else {
 			  		processAJAX($type, $messageText.value);
 			  }
		}
		
	}


function processAJAX($type, $msg)
{
	if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
	{
		$msg = encodeURIComponent($msg);
		xmlHttp.open("GET", "/iOS_push/sandbox.php?type=" + $type + "&msg=" + $msg, true);
		xmlHttp.onreadystatechange = handleServerResponse;
		xmlHttp.send(null);
	}
	else
		setTimeout('process()', 1000);
}


function handleServerResponse()
{
// move forward only if the transaction has completed
if (xmlHttp.readyState == 4)
{
// status of 200 indicates the transaction completed successfully
if (xmlHttp.status == 200)
{
// extract the XML retrieved from the server
xmlResponse = xmlHttp.responseXML;
// obtain the document element (the root element) of the XML structure
xmlDocumentElement = xmlResponse.documentElement;
// get the text message, which is in the first child of
// the the document element
helloMessage = xmlDocumentElement.firstChild.data;
// update the client display using the data received from the server
$responseText.innerHTML =
'<i>' + helloMessage + '</i>';

}
// a HTTP status different than 200 signals an error
else
{
alert("There was a problem accessing the server: " + xmlHttp.statusText);
}
}
}


function createXmlHttpRequestObject()
{
// will store the reference to the XMLHttpRequest object
var xmlHttp;
// if running Internet Explorer
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
// if running Mozilla or other browsers
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
// return the created object
if (!xmlHttp)
alert("Error creating the XMLHttpRequest object.");
else
return xmlHttp;

}





	window.onload = function() { 
	initializeVariables();
	};









