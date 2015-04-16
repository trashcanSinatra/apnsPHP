<?php

include('_classes/dataLink.class.php');
include('_classes/aspn.class.php');


$db = new dataLink();
$tokenMgr = new aspn($db);
$token = NULL;
$xml = new SimpleXMLElement('<xml/>');

if( isset( $_GET["id"])) {
	
	    $token = $_GET["id"];
	
		$storeToken = $tokenMgr->storeDeviceToken($token);
		
		if($storeToken) {
		
			$response = $xml->addChild('response');
			$response->addAttribute('message', "Your ID was Added.");
			Header('Content-type: text/xml');
			print($xml->asXML());
			exit;
		
		} else if(!$storeToken) {
		
			$response = $xml->addChild('response');
			$response->addAttribute('message', "That ID already exists.");
			Header('Content-type: text/xml');
			print($xml->asXML());
			exit;
		
		} else if ($storeToken == 2) {
		
			$response = $xml->addChild('response');
			$response->addAttribute('message', "The database could not be reached. Please try agin later.");
			Header('Content-type: text/xml');
			print($xml->asXML());
			exit;
		
		
		}
		
	
	
} else {
	
	exit;
}


























