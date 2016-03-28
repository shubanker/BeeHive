<?php
require_once '_includes/include_all.php';
if (isset($_GET['logout'])){
	Auth::logout();
}
if (!isset($_GET['s']) || empty($_GET['s'])){
	redirect_to();
}
$db=new Db();
$auth=new Auth($db);
if ($auth->is_login()){
	$user_id=$auth->get_userid();
	$user=new User($user_id,$db);
	$search=trim(urldecode($_GET['s']));
	if (preg_match('/^inpost:.*/i', $search)) {
		redirect_to("index.php?s=".$_GET['s']);//redirecting to index page if this is a in_post search.
	}
	$search_result=User::search($search, $db);
	if (empty($search_result)){
		$search_result=array();
	}
	include TEMPLATE.'search.html';
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