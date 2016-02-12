<?php
/*
 * Registration requirments.
 * Email
 * Password
 * Name
 * Gender
 * dob
 */
require_once '_includes/include_all.php';
require_once '../_includes/register.php';


if (isset($_GET['direct'])){//To escape slideshow.
	$_SESSION['show_over']=true;
}
if (isset($_GET['show_it'])){//To reshow slidshow.
	unset($_SESSION['show_over']);
}
//Checking to display show
if (!isset($_SESSION['show_over'])){
	include 'templates/reg-slideshow.html';
	$_SESSION['show_over']=true;
	closendie();
}
if (!empty($_POST)){
	$db=new Db(DBUSER, DBPASSWORD, DATABASE);
	if (!$db->isinit()){
		closendie("<h1>Database Error</h1>");
	}
	
	$register=new Register();
	$register->name($_POST['name']);
	$register->dob($_POST['dob']);
	$register->email($_POST['email']);
	$register->gender($_POST['gender']);
	$register->password($_POST['password']);
	
	if ($register->has_error()){
		$errors=$register->get_error();
	}else {
		$user_id=$register->register(true,$db);
		if ($user_id){
			$_SESSION['user_id']=$user_id;
			$_SESSION['user_name']=$_POST['name'];
			unset($_SESSION['show_over']);
			redirect_to();
		}else {
			$errors[]="Something Went Wrong :( <br/>Please try after sometime.";
		}
	}
}

include 'templates/beehive/reg-form.html';

?>