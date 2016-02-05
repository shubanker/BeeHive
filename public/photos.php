<?php
require_once '_includes/include_all.php';
if (isset($_GET['logout'])){
	Auth::logout();
}
$db=new Db();
$auth=new Auth($db);


if ($auth->is_login()){
	$user_id=$auth->get_userid();
	$user_name=null;
	if (isset($_GET['id'])&&is_numeric($_GET['id'])){
		$friend_id=(int)$_GET['id'];
		$friend=new User($friend_id,$db);
		$user_name=$friend->get_name();
		$gender=$friend->get_gen();
	}
	if (empty(trim($user_name))){
		$friend_id=$user_id;
		
		$friend=new User($friend_id,$db);
		$user_name=$friend->get_name();
		$gender=$friend->get_gen();
	}
	$is_self=$friend_id==$user_id;
	include TEMPLATE.'photos.html';
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