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
		$user_name=$_SESSION['user_name'];
	}
	/*
	 * will load it through AJAX
	 */
	$feeds=array();
// 	$feeds=Feeds::get_friends_feeds($user_id, $friend_id, $db); //uncomment this to load initial post's directly.
	$is_self=$friend_id==$user_id;
	include TEMPLATE.'profile.html';
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
</body>
</html>