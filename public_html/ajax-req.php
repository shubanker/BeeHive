<?php
require_once '../_includes/include_all.php';
require_once '../_includes/images.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);
if (!$db->isinit()){
	closendie(json_encode(array("error"=>"Database Error")));
}
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
	
}elseif (!isset($_POST['access_key']) || $_POST['access_key']!=$access_key){
	$responce['error']="Invalid AccessKey.";
	closendie(json_encode($responce));
}

$user_id=$auth->get_userid();
User::update_last_active($user_id, $db);
if (isset($_POST['req_type'])){
	$responce=null;
	switch ($_POST['req_type']){
		case "get_comments":
			$post_id=(int)$_POST['post_id'];
			$responce=Post::get_post_comments($user_id,$post_id, $db);
			for ($i = 0; $i < count($responce); $i++) {
				$responce[$i]['can_edit']=$responce[$i]['user_id']==$user_id?1:0;
			}
			$responce=make_time_redable($responce);
			$responce=make_html_entity($responce, array('comment','first_name','last_name'));
			break;
		case 'toggle_like':
			$post_id=(int)$_POST['post_id'];
			
			$r=$_POST['type']==1?Post::like_post($user_id, $post_id, $db):Post::unlike_post($user_id, $post_id, $db);
			$responce['success']=$r?1:0;
			break;
		case 'toggle_comment_like':
			$comment_id=(int)$_POST['comment_id'];
			
			$r=$_POST['type']==1?Post::like_comment($user_id, $comment_id, $db):Post::unlike_comment($user_id, $comment_id, $db);
			$responce['success']=$r?1:0;
			break;
		case 'syncpost':
			$last_post_id=isset($_POST['last_sync'])?(int)$_POST['last_sync']:0;
			$equality=$_POST['from_end']==1?"<":">";
			if (empty($_POST['friend_id'])){
				$options=array(
						"after_post_id"=>$last_post_id,
						"equality"=>$equality
				);
				$responce=Feeds::get_feeds($user_id, $db,$options);
			}else {
				$responce=Feeds::get_friends_feeds($user_id, (int)$_POST['friend_id'], $db,null,null,$last_post_id,$equality);
			}
			
			$responce=make_time_redable($responce);
			$responce=make_html_entity($responce, array('post_data','first_name','last_name'));
			
			#Can he edit/delete post
			for ($i = 0; $i < count($responce); $i++) {
				$responce[$i]['can_edit']=$responce[$i]['user_id']==$user_id?1:0;
			}
			break;
		case 'del_post':
			$post_id=(int)$_POST['post_id'];
			if (Post::set_post_status($user_id,$post_id, 0, $db)){
				$responce['success']=$db->affected_rows()==1?1:0;
			}else {
				$responce['success']=0;
			}
			
			break;
		case "del_comment":
			$comment_id=(int)$_POST['comment_id'];
			if (Post::remove_comment($comment_id, $db)){
				$responce['success']=$db->affected_rows()==1?1:0;
			}else {
				$responce['success']=0;
			}
			break;
		case "add_comment":
			$post_id=(int)$_POST['post_id'];
			$comment=trim($_POST['comment']);
			if (!empty($comment)){
				if ($comment_id=Post::add_comment($user_id, $post_id, $comment, $db)){
					$responce['comment_id']=$comment_id;
					$responce['time']=Feeds::get_age("now");
					$responce['full_time']=Feeds::get_age("now",true);
					$responce['user_id']=$user_id;
					$responce['first_name']=isset($_SESSION['user_name'])?htmlentities($_SESSION['user_name']):"You";
					$responce['last_name']="";
					$responce['comment']=htmlentities($_POST['comment']);
					$responce['can_edit']=1;
					$responce['like_count']=0;
					$responce['success']=1;
					$responce=make_html_entity($responce, array('comment','first_name','last_name'));
				}else {
					$responce['error']="There Went an Internal Error.";
				}
				
			}else {
				$responce['error']="Comment Can't be Empty";
			}
			break;
		case "new_post":
			$post_msg=empty($_POST['post_msg'])?null:$_POST['post_msg'];
// 			$post_msg_escaped=$db->escape($post_msg);
			$image_id=null;
			if (isset($_FILES['image']['name'])&&!empty($_FILES['image']['name'])){
				$image_id=image::new_image("image", $db);
				if (empty($image_id)){
					$responce['error']="Invalid Image File";
				}
			}
			if (empty($post_msg)&&empty($image_id)){
				$responce['error']=isset($responce['error'])?$responce['error']:"Post can not be Empty.";
				break;
			}
			$post=new Post();
			$post->set_user_id($user_id);
			$post->set_picture_id($image_id);
			$post->set_post_data($post_msg);
			$post->set_access(trim($_POST['privacy']));
			$post_id=$post->create($db);
			if (!empty($post_id)){
				$responce['success']=1;
				$responce['first_name']=isset($_SESSION['user_name'])?htmlentities($_SESSION['user_name']):"You";
				$responce['last_name']="";
				$responce['time']=Feeds::get_age("now");
				$responce['full_time']=Feeds::get_age("now",true);
				$responce['picture_id']=$image_id;
				$responce['comment_count']=$responce['like_count']=$responce['has_liked']=0;
				$responce['post_id']=$post_id;
				$responce['post_data']=htmlentities($post_msg);
				$responce['user_id']=$user_id;
				$responce['can_edit']=1;
			}
			$responce['error']="Something went Wrong";
			break;
		case 'editpost':
			if (is_numeric($_POST['post_id'])){
				$post_data=trim($_POST['post_data']);
				$post_id=(int)$_POST['post_id'];
// 				$responce[]=$_POST;
				if (Post::update_post_data($user_id, $post_id, $post_data, $db)){
					$responce['success']=$db->affected_rows()==1?1:0;
				}else {
					$responce['success']=0;
					$responce['error']='Something Went Wrong.';
				}
			}else{
				$responce['success']=0;
				$responce['error']='Invalid Post Id';
				
			}
			
			break;
		case 'editcomment':
			if (is_numeric($_POST['comment_id'])){

				$new_comment=trim($_POST['comment_data']);
				$comment_id=(int)$_POST['comment_id'];
				if (Post::edit_comment($comment_id, $new_comment, $db,$user_id)==1){
					$responce['success']=1;
					$responce['comment']=$new_comment;
					make_html_entity($responce, array('comment'));
				}else {
					$responce['success']=0;
					$responce['error']='Something Went Wrong.';
				}
			}else{
				$responce['success']=0;
				$responce['error']='Invalid Comment Id';
				
			}
			break;
		case "online_list":
			$responce=Message::get_chat_list($user_id, $db);
			$now=strtotime("now");
			for ($i=0;$i<count($responce);$i++){
				$responce[$i]['data']=$now-$responce[$i]['data'];
			}
			if (count($responce)==0){
				$responce['go_to_friends_page']=1;
			}
			break;
		case "get_msg":
			if (is_numeric($_POST['friendid']) &&(int)$_POST['friendid']>0){
				$friend_id=(int)$_POST['friendid'];
				$last_sync=isset($_POST['lastsync'])?(int)$_POST['lastsync']:0;
				$equality=$_POST['fillbefore']==0?"<":">";
				$responce=Message::get_messages($user_id, $friend_id, $db,null,null,$last_sync,$equality);
				
				//For marking read.
				$message_ids=array();
				foreach ($responce as $msg){
					if ($msg['user_two']==$user_id){
						$message_ids[]=$msg['message_id'];
					}
				}
				Message::mark_received($user_id,$message_ids, $db);
				
				$responce=array_reverse($responce);
				$responce=make_html_entity($responce, array('message'));
				$responce=make_time_redable($responce);
			}else{
				$responce['error']='invalid reques';
			}
			break;
		case "send_msg":
			$msg=trim($_POST['msg']);
			if (empty($msg)||!is_numeric($_POST['friendid'])){
				$responce['error']="Invalid request";
			}else {
				$friend_id=(int)$_POST['friendid'];
				$message_id=Message::send_message($user_id, $friend_id, $msg, $db);
				if ($message_id){
					$responce['message_id']=$message_id;
					$responce['user_one']=$user_id;
					$responce['user_two']=$friend_id;
					$responce['message']=$_POST['msg'];
					$responce['time']=Feeds::get_age("now");
					$responce['full_time']=Feeds::get_age("now",true);
					$responce['status']=1;
					$responce['success']=1;
				}else {
					$responce['success']=0;
				}
			}
			break;
		case "notification_count":
			$responce['notification_count']=Notifications::get_notification_count($user_id,$db);
			$responce['message_count']=Message::get_unread_count($user_id, $db);
			break;
		case "get_friend_list":
			$friend_id=(int)$_POST['friend_id'];
			$limit=(int)$_POST['limit'];
			$result=Friendship::get_mutuals($user_id, $friend_id, $db);
			shuffle($result);
			$responce=array_slice($result, 0,$limit);
			break;
		case "get_Images":
			$friend_id=(int)$_POST['friend_id'];
			$options=array();
			$limit=is_numeric($_POST['limit'])?(int)$_POST['limit']:null;
			if (!empty($limit)){
				$options['limit']=$limit;
			}
			if (!empty($_POST['images_sync'])){
				$options['alerady_sync']=$_POST['images_sync'];
			}
			$responce=Feeds::get_friends_images($user_id, $friend_id, $db,$options);
			break;
		case 'change_dp':
			$responce['success']=0;
			$image_id=(int)$_POST['img_id'];
			if (!empty($image_id)&&$image_id>0){
				
				//Checking if Image exists..
				
				
				if (image::image_exists($image_id, $db) && UserData::edit_by_type($user_id, 'dp', $image_id, $db)){
					$responce['success']=1;
				}
			}
			break;
		case 'friend_action':
			$friend_id=(int)$_POST['friend_id'];
			if ($friend_id==$user_id){
				$responce['error']="Cant Have relationship with self";
				break;
			}
			$relation=Friendship::get_relation($user_id, $friend_id, $db,false);
			if ($relation!=0){
				if ($relation['status']==1){
					if ($relation['user_one']==$user_id){
						Friendship::cancle_friend_req($user_id, $friend_id, $db);
						$responce['new_action']="Add Friend";
						$responce['success']=1;
					}else {
						Friendship::accept_request($friend_id, $user_id, $db);
						$responce['new_action']="Un Friend";
						$responce['success']=1;
					}
				}elseif ($relation['status']==2){
					Friendship::unfriend($user_id, $friend_id, $db);
					$responce['new_action']="Add Friend";
					$responce['success']=1;
				}elseif ($relation['status']==0) {
					
					Friendship::send_friend_req($user_id, $friend_id, $db);
					$responce['success']=1;
					$responce['new_action']="Cancle Request";
					
				}elseif ($relation['status']==3){
					
					if ($relation['user_one']==$user_id){
						Friendship::unblock($user_id, $friend_id, $db);
						$responce['success']=1;
						$responce['new_action']="Add Friend";
					}else {
						$responce['success']=0;
					}
					
				}
			}else {
				if (Friendship::send_friend_req($user_id, $friend_id, $db)){
					$responce['success']=1;
					$responce['new_action']="Cancle Request";
				}
				
			}
			break;
		case 'block_user':
			$friend_id=(int)$_POST['friend_id'];
			if (!empty($friend_id)){
				if (Friendship::block($user_id, $friend_id, $db)){
					$responce['success']=1;
				}
			}else {
				$responce['error']=1;
			}
			break;
		case 'get_connection_list':
			$friend_id=(int)$_POST['friend_id'];
			$result=Friendship::get_all_connections($user_id, $db,$friend_id==$user_id);
			$responce=array();
			foreach ($result as $r){
				if ($r['connection']=='isblocked'){//No users should know wo has blocked him/her
					continue;
				}
				$responce[]=$r;
			}
			break;
		case 'messages_list':
			$recent_messages=Message::get_recent_message_list($user_id, $db,0,15);
			$recent_messages=make_time_redable($recent_messages);
			$responce=make_html_entity($recent_messages, array("message","first_name","last_name"));
			break;
		case 'get_notifications':
			$after=!empty($_POST['lastsync']) && is_numeric($_POST['lastsync'])?(int)$_POST['lastsync']:null;
			$responce=Notifications::get_notifications($user_id,null,$db,null,null,$after);
			$responce=make_time_redable($responce);
			$responce=make_html_entity($responce,array('message'));
			$notification_ids=array();
			foreach ($responce as $noti){
				$notification_ids[]=$noti['notification_id'];
			}
			if (!empty($notification_ids)){
				Notifications::mark_read($notification_ids, $db);
			}
			break;
		case 'get_post':
			$post_id=(int)$_POST['post_id'];
			if (!empty($post_id)){
				$result=Feeds::get_feeds($user_id, $db,array("specific_post_id"=>$post_id));
				$responce['success']=1;
				if (!empty($result)){
					$responce[0]=isset($result[0])?$result[0]:array();
					$responce[0]['can_edit']=$responce[0]['user_id']==$user_id?1:0; 
					$responce=make_time_redable($responce);
				}else{
					$responce['success']=0;
					$responce['error']="Invalid Post.";
				}
			}else {
				$responce['error']="Invalid Request";
			}
			break;
		case 'search_in_post':
			$search_key=trim($_POST['search_key']);
			$hash=$inpost_key=null;
			switch ($_POST['req']){
				case "hash":
					$hash=$search_key;
					break;
				case "inpost":default:$inpost_key=$search_key;
			}
			$options=array("hash_tag"=>$hash,
					"in_post"=>$inpost_key
			);
			$responce=Feeds::get_feeds($user_id, $db,$options);
			
			$responce=make_time_redable($responce);
			$responce=make_html_entity($responce, array('post_data','first_name','last_name'));
				
			#Can he edit/delete post
			for ($i = 0; $i < count($responce); $i++) {
			$responce[$i]['can_edit']=$responce[$i]['user_id']==$user_id?1:0;
			}
			break;
		default:$responce['error']="Invalid Request";
		
	}
	closendie(json_encode($responce));
}
function make_time_redable($array,$field="time"){
	for ($i=0;$i<count($array);$i++){
		if (isset($array[$i][$field])){
			$array[$i][$field]=Feeds::get_age($array[$i][$field]);
			$array[$i]['full_time']=Feeds::get_age($array[$i][$field],true);
		}
	}
	return $array;
}
function make_html_entity($arrays,$fields){
	for ($i = 0; $i < count($arrays); $i++) {
		foreach ($fields as $field){
			if (!isset($arrays[$i][$field])){
				continue;
			}
				$arrays[$i][$field]=htmlentities($arrays[$i][$field]);
		}
	}
	return $arrays;
}