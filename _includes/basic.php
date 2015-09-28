<?php
function isLocal() {
	return $_SERVER ["SERVER_ADDR"] == "::1" || $_SERVER ["SERVER_ADDR"] == "127.0.0.1";
}
function redirect_to($location = "index.php") {
	headers_sent () ? die ( "<META HTTP-EQUIV=\"refresh\" content=\"0; URL='$location'\">" ) : header ( "location: $location" );
}
function closendie ($msg="",$db=null){
	if (empty($db)){
		global $db;
	}
	if (!empty($db)){
		$db->close();
	}
	die($msg);
}