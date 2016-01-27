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
	/*
	 * will load it through AJAX
	 */
	$feeds=array();
// 	$feeds=Feeds::get_feeds($user_id, $db); //uncomment this to load initial post's directly.
	
	include TEMPLATE.'home.html';
	if (!empty($db) && $db->isinit()){
		User::update_last_active($user_id, $db);
	}
}else {
	Auth::do_login();
	include TEMPLATE.'loginhome.html';
}
if (!empty($db) && $db->isinit()){
	$db->close();
}
?>
</body>
</html>