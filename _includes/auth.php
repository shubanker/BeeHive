<?php
class Auth{
	private $user_id;
	private $error;
	private $is_login;
	function __construct($db){
		$this->is_login=$this->user_id=false;
		$this->error=array();
		
		if(isset($_SESSION['user_id'])){
			$this->user_id=$_SESSION['user_id'];
			$this->is_login=true;
		}else {
			$this->check_user($db);
		}
	}
	function check_user($db){
		if (isset($_SESSION['user_id'])){
			$this->is_login=true;
			return $this->user_id=$_SESSION['user_id'];
		}
		if (empty($db)){
			return false;
		}
		if ($user_id=Cookies::verify_cookies(null, null, $db)){
			$_SESSION['user_id']=$this->user_id=$user_id;
			$user=new User($user_id,$db);
			$_SESSION['user_name']=$user->get_name();
			$this->is_login=TRUE;
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
				$this->is_login=TRUE;
				$_SESSION['user_name']=$user->get_name();
				return $this->user_id=$_SESSION ['user_id'] =$user->get_user_id();
			}
		}
		$this->set_error("Invalid email/password.");//if we are still here
		return false;
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