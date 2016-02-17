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
	if (!isset($friend) || !trim($friend->get_user_id()) || $friend->get_user_id()!=$friend_id){
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
	if ($is_self && !empty($_POST)){
		$friend->set_first_name($_POST['first_name']);
		$friend->set_last_name($_POST['last_name']);
		$friend->set_dob($_POST['birthday']);
		$friend->set_email($_POST['email']);
		$friend->set_gender($_POST['gender']);
		$friend->update($db);
		
		$data=array();
		foreach ($About_data_list as $about_list){
			$about_list_key=strtolower(str_replace(" ", "_", $about_list));
			if (!empty($_POST[$about_list_key])){
				$data[]=array(
						"user_id"=>$user_id,
						"type"=>$about_list,
						"data"=>$_POST[$about_list_key],
						"status"=>1
				);
			}
		}
		if (!empty($data)){
			UserData::insert_multiple($data, $db);
		}
	}
	$abouts["First Name"]=$friend->get('first_name');
	$abouts["Last Name"]=$friend->get('last_name');
	$abouts["Birthday"]=$friend->get_dob();
	$abouts["Email"]=$friend->get_email();
	$abouts["Gender"]=$friend->get_gen()=='M'?'Male':'Female';

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