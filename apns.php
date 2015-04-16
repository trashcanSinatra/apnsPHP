<?php
include('_classes/dataLink.class.php');
include('_classes/push.class.php');
include('_classes/apns.class.php');
include('_classes/debug.class.php');
include('_classes/getDebug.class.php');


if (isset($_GET['msg']) & isset($_GET['type'])) {

	$type = $_GET['type'];
	$msg = $_GET['msg'];
	$db = new dataLink();
	$aspn = new apns($db);
	$push = new push($aspn, true);

	
	$response = $push->delegatePush($type, $msg);
	echo $response;	
	
} else {
	exit();
}

?>
}
?>
