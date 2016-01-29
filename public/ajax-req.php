<?php
require_once '../_includes/include_all.php';
require_once '../_includes/images.php';
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
			$comments=make_time_redable($comments);
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
			$feeds=make_time_redable($feeds);
			closendie(json_encode($feeds));
			break;
		case "add_comment":
			$post_id=(int)$_POST['post_id'];
			$comment=$db->escape($_POST['comment']);
			$responce['comment_id']=Post::add_comment($user_id, $post_id, $comment, $db);
			$responce['time']=Feeds::get_age("now");
			$responce['user_id']=$user_id;
			$responce['first_name']=$_SESSION['user_name'];
			$responce['last_name']="";
			$responce['comment']=$_POST['comment'];
			
			closendie(json_encode($responce));
			break;
		case "new_post":
			$post_msg=empty($_POST['post_msg'])?null:$_POST['post_msg'];
			$post_msg_escaped=$db->escape($post_msg);
			$image_id=null;
			if (isset($_FILES['image']['name'])&&!empty($_FILES['image']['name'])){
				$image_id=image::new_image("image", $db);
				if (empty($image_id)){
					$responce['error']="Invalid Image";
					closendie(json_encode($responce));
				}
			}
			if (empty($post_msg)&&!isset($responce['error'])&&empty($image_id)){
				$responce['error']="Post can not be Empty.";
				closendie(json_encode($responce));
			}
			$post=new Post();
			$post->set_user_id($user_id);
			$post->set_picture_id($image_id);
			$post->set_post_data($post_msg_escaped);
			$post->set_access(2);
			$post_id=$post->create($db);
			if (!empty($post_id)){
				$responce['success']=1;
				$responce['first_name']=$_SESSION['user_name'];
				$responce['last_name']="";
				$responce['time']=Feeds::get_age("now");
				$responce['picture_id']=$image_id;
				$responce['comment_count']=$responce['like_count']=$responce['has_liked']=0;
				$responce['post_id']=$post_id;
				$responce['post_data']=$post_msg;
				$responce['user_id']=$user_id;
				closendie(json_encode($responce));
			}
			$responce['error']="Something went Wrong";
			closendie(json_encode($responce));
			break;
		case "online_list":
			$list=Message::get_chat_list($user_id, $db);
			$now=strtotime("now");
			for ($i=0;$i<count($list);$i++){
				$list[$i]['data']=$now-$list[$i]['data'];
			}
			closendie(json_encode($list));
			break;
		case "get_msg":
			if (is_numeric($_POST['friendid'])){
				$friend_id=(int)$_POST['friendid'];
				$last_sync=(int)$_POST['lastsync'];
				$msg=Message::get_messages($user_id, $friend_id, $db,null,null,$last_sync,false);
				$msg=make_time_redable($msg);
				closendie(json_encode($msg));
			}
			closendie(json_encode(array('error'=>'invalid request')));
			break;
		case "send_msg":
			if (empty(trim($_POST['msg']))||!is_numeric($_POST['friendid'])){
				$responce['error']="Invalid request";
			}else {
				$smsg=$db->escape(trim($_POST['msg']));
				$friend_id=(int)$_POST['friendid'];
				$message_id=Message::send_message($user_id, $friend_id, $smsg, $db);
				if ($message_id){
// 					$responce['message_id']=$message_id;
// 					$responce['user_one']=$user_id;
// 					$responce['user_two']=$friend_id;
// 					$responce['message']=$_POST['msg'];
// 					$responce['time']=Feeds::get_age("now");
// 					$responce['status']=1;
					$responce['success']=1;
				}else {
					$responce['success']=0;
				}
			}
			closendie(json_encode($responce));
			break;
	}
}
function make_time_redable($array,$field="time"){
	for ($i=0;$i<count($array);$i++){
		$array[$i][$field]=Feeds::get_age($array[$i][$field]);
	}
	return $array;
}