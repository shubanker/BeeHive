<?php

require_once '_includes/include_all.php';
echo clearNoteHtmlOp::get_head();
echo clearNoteHtmlOp::get_include_css();

if (isset($_GET['logout'])){
	Auth::logout();
}
$db=new Db();
$auth=new Auth($db);


if ($auth->is_login()){
	$user_id=$auth->get_userid();
	$friend_id=isset($_GET['id'])&&is_numeric($_GET['id'])?(int)$_GET['id']:$user_id;
	/*
	 * will load it through AJAX
	 */
	$feeds=array();
// 	$feeds=Feeds::get_friends_feeds($user_id, $friend_id, $db); //uncomment this to load initial post's directly.
	$user_name=$_SESSION['user_name'];
	include TEMPLATE.'profile.html';
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