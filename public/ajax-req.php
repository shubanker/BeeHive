<?php
require_once '../_includes/include_all.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);
if (isset($_REQUEST['email_exists'])){
	$result['email_exists']=User::email_registered(trim($_REQUEST['email_exists']), $db)?1:0;
	die(json_encode($result));
	
}
if (isset($_POST['req_type'])){
	switch ($_POST['req_type']){
		case "login":
			$auth=new Auth($db);
			$responce['success']=0;
			if ($auth->check_crediantials($_POST['email'], $_POST['password'], $db)){
				$responce['success']=1;
			}else {
				$responce['msg']=$auth->get_error();
			}
			closendie(json_encode($responce));
			break;
		case "get_comments":
			$post_id=(int)$_POST['post_id'];
			$comments=Post::get_post_comments($post_id, $db);
			for ($i = 0; $i < count($comments); $i++) {
				$comments[$i]['time']=Feeds::get_age($comments[$i]['time']);
			}
			closendie(json_encode($comments));
			break;
			
	}
}