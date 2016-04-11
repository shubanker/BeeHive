<?php
class Auth{
	private $user_id;
	private $error;
	private $is_login;
	function __construct($db){
		$this->is_login=$this->user_id=false;
		$this->error=array();
		
		if(isset($_SESSION['user_id'])&&is_numeric($_SESSION['user_id'])){
			$this->user_id=$_SESSION['user_id'];
			$this->is_login=true;
		}else {
			$this->check_user($db);
		}
	}
	function check_user($db){
		if (isset($_SESSION['user_id'])&&is_numeric($_SESSION['user_id'])){
			$this->is_login=true;
			return $this->user_id=$_SESSION['user_id'];
		}
		if (empty($db)){
			return false;
		}
		if ($user_id=Cookies::verify_cookies(null, null, $db)){
			$this->set_login($user_id, $db);
		}
	}
	function check_crediantials($email,$password,$db){
		if (!$db->isinit()){
			$this->set_error("Something went wrong :( \nPlease try again after Sometime.");
			return false;
		}
		if (!validate::email($email)){
			$this->set_error('Invalid Email');
			return false;
		}
		
		if (empty($password)){
			$this->set_error("Password Can not be Empty");
			return false;
		}
		$user=new User();
		if ($user->initialise_by_email($db, $email)){
			if ($user->check_password($password)){
				return $this->set_login(null, $db,$user);
			}
		}
		$this->set_error("Invalid email/password.");//if we are still here
		return false;
	}
	private function set_login($user_id,$db,$user=null){
		if (empty($user)){
			if (empty($user_id)){
				return false;
			}
			$user=new User($user_id,$db);
		}
		if (empty($user_id)){
			$user_id=$user->get_user_id();
		}
		$this->is_login=TRUE;
		$_SESSION['user_name']=$user->get_name();
		$GLOBALS['access_key'] = $_SESSION['access_key']=Keys::get_random_string(8,20);
		return $this->user_id=$_SESSION ['user_id'] =$user->get_user_id();
	}
	function get_userid(){
		return $this->user_id;
	}
	function is_login(){
		return $this->is_login;
	}
	function get_error(){
		return $this->error;
	}
	private function set_error($error){
		$this->error=$_SESSION['msg']=$error;
		$_SESSION['msg_type']="danger";
	}
	/*
	 * Function handles login,password recovery requests.
	 */
	static function do_login($db=NULL){
		if (isset($_POST['email'])){
			if (isset($_POST['password'])){
				if (empty($db)){
					$db=new Db();
				}
				$auth=new Auth($db);
				if ($auth->check_crediantials($_POST['email'], $_POST['password'], $db)){
					Cookies::create_cookie($auth->user_id, $db);
					session_regenerate_id();
					redirect_to();
					closendie("",$db);
				}
			}elseif (isset($_POST['forgot'])){
				if (!empty($_POST['email'])&&validate::email($_POST['email'])){
					if (User::email_registered(trim($_POST['email']), $db)){
						$user=new User();
						$user->initialise_by_email($db, trim($_POST['email']));
						$key=Keys::gen_key($user->get_user_id(), "24 hours", $db);
						NotificationEmails::send_password_recovery_email($user, $key);
					}
					$_SESSION['msg']="Email Sent Check Your Inbox";
					$_SESSION['msg_type']="success";
				}else {
					$_SESSION['msg']="Invalid Email.";
					$_SESSION['msg_type']="danger";
				}
			}else {
				$_SESSION['msg']="Unknown request.";
				$_SESSION['msg_type']="danger";
			}
		}
	}
	static function logout($db=NULL){
		if (empty($db)){
			$db=new Db();
		}
		if (isset( $_COOKIE ['user_id'])){
			Cookies::deactivate_cookie($_COOKIE ['user_id'], $_COOKIE ['token'], $db);
		}
		session_unset();
		redirect_to();
		closendie("",$db);
	}
}