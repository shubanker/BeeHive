<?php
require_once '_includes/include_all.php';

if (isset($_POST['new_password'])){
	$key=$_POST['token_key'];
	$user_id=(int)$_POST['user_id'];
}elseif (isset($_GET['token'])&& isset($_GET['user'])){
	$key=$_GET['token'];
	$user_id=(int)$_GET['user'];
}
$is_valid=false;
$msg=array();

//Checking if key is valid.
if (isset($key)){
	$db=new Db();
	$key_data=Keys::get_key_data($user_id, $key, $db,1);
	$is_valid=Keys::is_valid($key_data);
}

if (!$is_valid){
	$msg[]="Invalid toeken or the link may have expired.";
	echo implode("<br>", $msg);
}else {
	if (isset($_POST['new_password'])){
		
		if ($_POST['new_password'] != $_POST['confirm_password']){
			$msg[]="Password Did not match please try again.";
			
		}elseif (!validate::password($_POST['new_password'])){
			$msg[]="Password should be between 7 & 35 characters Long.";
			
		}else{
			$user=new User($user_id,$db);
			$user->set_password($_POST['new_password']);
			
			if ($user->update($db)){
				Cookies::clear_logins($user_id, null, $db,true);
				$_SESSION['msg']="Password successfully Updated, Please Login with your new Password.";
				$_SESSION['msg_type']="success";
				unset($_SESSION['mail']);
				redirect_to();
				closendie();
			}else {
				$msg[]="There went an internal error while updating your Password";
			}
		}
	}
	include TEMPLATE.'password_recovery.html';
}
if (!empty($db) && $db->isinit()){
	$db->close();
}
?>