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
	if (!isset($friend) || empty($friend->get_user_id()) || $friend->get_user_id()!=$friend_id){
		$friend_id=$user_id;
		
		$friend=new User($friend_id,$db);
		$user_name=$friend->get_name();
		$gender=$friend->get_gen();
	}
	$is_self=$friend_id==$user_id;
	if (!$is_self){
		$relation=Friendship::get_relation($user_id, $friend_id, $db,false);
		$friend_button=Friendship::get_action($user_id, $friend_id, $db,$relation);
	}
	
	/*
	 * List of What possible details can be displayed/Added in about Page.
	 */
	$About_data_list=array(
			"Mobile",
			"Phone",
			"Occupation",
			"Country",
			"School",
			"High School",
			"College"
	);
	$abouts=array(
			"First Name"=>$friend->get('first_name'),
			"Last Name"=>$friend->get('last_name'),
			"Birthday"=>$friend->get_dob(),
			"Email"=>$friend->get_email(),
			"Gender"=>$friend->get_gen()=="M"?"Male":"Female"
	);
	$user_data=UserData::get_all_data($friend_id, $db);
	foreach ($user_data as $data){
		if (in_array($data['type'], $About_data_list)){
			$abouts[$data['type']]=$data['data'];
		}
	}
	if ($is_self){
		foreach ($About_data_list as $about_list){
			$abouts[$about_list]=isset($abouts[$about_list])?$abouts[$about_list]:null;
		}
	}
	
	
	include TEMPLATE.'about.html';
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