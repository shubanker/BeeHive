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
	if (!isset($friend) || $friend->get_user_id()!=$friend_id){
		$friend_id=$user_id;
		
		$friend=new User($friend_id,$db);
		$user_name=$friend->get_name();
		$gender=$friend->get_gen();
	}
	$is_self=$friend_id==$user_id;
	if (!$is_self){
		$relation=Friendship::get_relation($user_id, $friend_id, $db,false);
		$friend_button=Friendship::get_action($user_id, $friend_id, $db,$relation);
		
		if (!isset($relation['status'])||$relation['status']!=2){//Stopping from Public view.
			redirect_to();
			closedir();
		}
	}
	
	$connections=Friendship::get_all_connections($friend_id, $db,$is_self);
	$connections_arranged=array();
	/*
	 * Arranging connections according to its type
	 */
	foreach ($connections as $c){
		$connections_arranged[$c['connection']][]=$c;
	}
	
	/*
	 * Tabs to show..
	 */
	$tabs=array("friends","following","followers");
	if ($is_self){
		$tabs[]="blocked";
	}
	
	/*
	 * Index of Buttons action.
	 */
	$action_button_text=array(
			"friends"=>"Un Friend",
			"following"=>"Cancle Request",
			"followers"=>"Accept Request",
			"blocked"=>"Un Block"
	);
	include TEMPLATE.'friends.html';
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