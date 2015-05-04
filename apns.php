<?php
include('_classes/dataLink.class.php');
include('_classes/push.class.php');
include('_classes/apns.class.php');
include('_classes/debug.class.php');
include('_classes/getDebug.class.php');

$db = new dataLink();
$aspn = new apns($db);
$push = new push($aspn, true);


if (isset($_GET['msg']) & isset($_GET['type'])) {

	$type = $_GET['type'];
	$typeArray = array('flush', 'store', 'store_push');
	
		if(!in_array($type, $typeArray)) {
			exit();
		}	
		$msg = $_GET['msg'];
		$response = $push->delegatePush($type, $msg);
		echo $response;

} elseif (isset($_GET['pushCode'])) {
	
	$pushCode = $_GET['pushCode'];
	if($pushCode != "49 5c a4 f7 78 a2 4f d4 99 a4") {	exit(); }

		$xmlResponse = $push->finalPush();
		echo $xmlResponse;
		exit();

} else {
	exit();
}


?>