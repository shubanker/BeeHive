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
	$hash_tag=isset($_GET['hashtag'])?trim($_GET['hashtag']):null;
	$in_post=null;
	if (isset($_GET['s'])){//if we have a inpost search.
		$search_item=rawurldecode($_GET['s']);
		if (preg_match('/^inpost:.*/i', $search_item)) {
			$in_post=substr($search_item, 7);
		} else {
			redirect_to("search.php?s=".$_GET['s']);//redirecting to search page if this is not a in_post search.
		}
	}
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