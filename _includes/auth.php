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
			return $this->user_id=$_SESSION['user_id'];
		}
		
		if ($id=Cookies::verify_cookies(null, null, $db)){
			$_SESSION['user_id']=$this->user_id=$id;
			$this->is_login=TRUE;
		}
	}
	function check_crediantials($email,$password,$db){
		
		if (!validate::email($email)){
			$this->error['email']='Invalid Email';
			return false;
		}
		
		/*
		 * Lets escape email.
		 */
		$s_email=$db->escape($email);
		
		$sql=Db::create_sql(array('user_id','password'), array('users'),"email='$s_email' AND status=1");
		$data=Db::fetch_array($db, $sql);
		
		/*
		 * Making shure only one data is returned
		 */
		if (count($data)==1 && password::verify_password($password, $data[0]['password'])){
			$this->is_login=TRUE;
			$_SESSION ['id'] =(int)$data[0]['user_id'];
			return $this->user_id=(int)$data[0]['user_id'];
		}
		$this->error['crediantials']="Invalid email/password.";
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
}