<?php
function isLocal() {
	$local_address=array(
			"::1",
			"127.0.0.1",
			"192.168.172.1",
			"192.168.1.3"
	);
	return in_array($_SERVER ["SERVER_ADDR"], $local_address);
}
function redirect_to($location = "index.php") {
	headers_sent () ? die ( "<META HTTP-EQUIV=\"refresh\" content=\"0; URL='$location'\">" ) : header ( "location: $location" );
}
function closendie ($msg="",$db=null){
	if (empty($db)){
		global $db;
	}
	if (!empty($db) && $db->isinit()){
		$db->close();
	}
	die($msg);
}