<?php
require_once '../_includes/include_all.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);
$auth=new Auth($db);
if (isset($_REQUEST['email_exists'])){
	$result['email_exists']=User::email_registered(trim($_REQUEST['email_exists']), $db)?1:0;
	die(json_encode($result));
	
}
if (isset($_POST['login'])){
	$responce['success']=0;
	if ($auth->check_crediantials($_POST['email'], $_POST['password'], $db)){
		$responce['success']=1;
	}else {
		$responce['msg']=$auth->get_error();
	}
	closendie(json_encode($responce));
}
/*
 * If user isn't logged in he can't access any of code below.
 */
if (!$auth->is_login()){
	$responce['error']="access";
	closendie(json_encode($responce));
}

$user_id=$auth->get_userid();
User::update_last_active($user_id, $db);
if (isset($_POST['req_type'])){
	switch ($_POST['req_type']){
		case "get_comments":
			$post_id=(int)$_POST['post_id'];
			$comments=Post::get_post_comments($post_id, $db);
			for ($i = 0; $i < count($comments); $i++) {
				$comments[$i]['time']=Feeds::get_age($comments[$i]['time']);
			}
			closendie(json_encode($comments));
			break;
		case 'toggle_like':
			$post_id=(int)$_POST['post_id'];
			
			$r=$_POST['type']==1?Post::like_post($user_id, $post_id, $db):Post::unlike_post($user_id, $post_id, $db);
			$responce['success']=$r?1:0;
			closendie(json_encode($responce));
			break;
		case 'syncpost':
			$last_post_id=isset($_POST['last_sync'])?(int)$_POST['last_sync']:0;
			$feeds=Feeds::get_feeds($user_id, $db,null,null,$last_post_id);
			for($i=0;$i<count($feeds);$i++){
				$feeds[$i]['time']=Feeds::get_age($feeds[$i]['time']);
			}
			closendie(json_encode($feeds));
			break;
		case "add_comment":
			$post_id=(int)$_POST['post_id'];
			$comment=$db->escape($_POST['comment']);
			$responce['comment_id']=Post::add_comment($user_id, $post_id, $comment, $db);
			closendie(json_encode($responce));
			break;
	}
}