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
		$relation=Friendship::get_relation($user_id, $friend_id, $db,false);
		if ($relation['status']==3){
			redirect_to();
			closendie();
		}
	}
	$recent_messages=array();
// 	$recent_messages=Message::get_recent_message_list($user_id, $db);
	if (!isset($friend_id)||empty($friend_id)){
// 		$friend_id=$recent_messages[0]['user_id'];
		$friend_id=0;
	}
	include TEMPLATE.'messages.html';
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