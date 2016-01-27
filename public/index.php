<?php

require_once '_includes/include_all.php';
echo clearNoteHtmlOp::get_head();
echo clearNoteHtmlOp::get_include_css();

if (isset($_GET['logout'])){
	Auth::logout();
}
$db=null;//Initialising variable.
//Checking if database is required.
if (isset($_SESSION['user_id'])||!empty($_POST)){
	$db=new Db();
}
$auth=new Auth($db);


if ($auth->is_login()){
	$user_id=$auth->get_userid();
	$user=new User($user_id,$db);
	$user->update_last_active($user_id, $db);
	/*
	 * Loading it through AJAX
	 */
	$feeds=array();
// 	$feeds=Feeds::get_feeds($user_id, $db);
	
	include TEMPLATE.'home.html';
}else {
	Auth::do_login();
	include TEMPLATE.'loginhome.html';
}
echo clearNoteHtmlOp::get_js();
if (!empty($db) && $db->isinit()){
	$db->close();
}
?>
</body>
</html>