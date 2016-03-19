<?php
require_once '_includes/include_all.php';
if (isset($_GET['logout'])){
	Auth::logout();
}
$db=new Db();
$auth=new Auth($db);
if ($auth->is_login()){
	$user_id=$auth->get_userid();
	$user=new User($user_id,$db);
// 	$msg="none";
	if (isset($_POST['old_pass']) && isset($_POST['new_pass']) && !empty($_POST['new_pass'])){
		if ($user->check_password($_POST['old_pass'])){
			if (validate::password($_POST['new_pass'])){
				$user->set_password($_POST['new_pass']);
				$user->update($db);
				$msg="Password Updated";
				$msg_type="success";
			}else {
				$msg="Password should be between 7 & 35 characters Long.";
				$msg_type="danger";
			}
		}else {
			$msg="Incorrect Password";
			$msg_type="danger";
		}
	}
	if (isset($_GET['clear_otherlogins'])){
		if (!isset($_GET['access_key']) || $_GET['access_key']!=$access_key){
			$msg="Invalid AccessKey";
			$msg_type="danger";
		}else {
			$result=Cookies::clear_logins($user_id, null, $db);
			if (is_numeric($result) && $result >= 0){
				$msg="Cleared $result Logins";
				$msg_type="success";
			}else {
				$msg="Something went Wrong..";
				$msg_type="danger";
			}
		
		}
	}
	$active_logins=Cookies::active_logins_count($user_id, $db,isset($_COOKIE['token'])?$_COOKIE['token']:'');
	include TEMPLATE.'account.html';
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