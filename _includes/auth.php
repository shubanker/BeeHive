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
		if ($id=Cookies::verify_cookies(null, null, $db)){
			$_SESSION['user_id']=$this->user_id=$id;
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
		/*
		 * Lets escape email.
		 */
		$s_email=$db->escape($email);
		$sql=Db::create_sql(array('user_id','password','first_name','last_name'), array('users'),"email='$s_email' AND status=1");
		if ($data=Db::fetch_array($db, $sql)){
			/*
			 * Making shure only one data is returned
			 */
			if (count($data)==1 && password::verify_password($password, $data[0]['password'])){
				$this->is_login=TRUE;
				$_SESSION ['user_id'] =(int)$data[0]['user_id'];
				$_SESSION['user_name']=$data[0]['first_name']." ".$data[0]['last_name'];
				return $this->user_id=(int)$data[0]['user_id'];
			}
			$this->set_error("Invalid email/password.");
		}else {
			$this->set_error("Something went wrong :( \nPlease try again after Sometime.");
		}
		
		
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