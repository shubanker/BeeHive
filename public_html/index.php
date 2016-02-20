<?php

require_once '_includes/include_all.php';
if (isset($_GET['logout'])){
	Auth::logout();
}
$db=new Db();
$auth=new Auth($db);


if ($auth->is_login()){
	$user_id=$auth->get_userid();
	/*
	 * will load it through AJAX
	 */
	$feeds=array();
// 	$feeds=Feeds::get_feeds($user_id, $db); //uncomment this to load initial post's directly.
	
	$post_id=isset($_GET['post'])&&is_numeric($_GET['post'])?(int)$_GET['post']:null;
	include TEMPLATE.'home.html';
	if (!empty($db) && $db->isinit()){
		User::update_last_active($user_id, $db);
	}
}else {
	Auth::do_login($db);
	include TEMPLATE.'loginhome.html';
}
if (!empty($db) && $db->isinit()){
	$db->close();
}
?>