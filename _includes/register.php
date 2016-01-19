<?php
class Register{
	private $error;
	private $user;
	function __construct(){
		$this->error=null;
		$this->user=new User();
	}
	function get_error(){
		return $this->error;
	}
	function has_error(){
		return isset($this->error);
	}
	function register($cerate=false,$db=NULL){
		if ($this->has_error()){
			return false;
		}
		$this->user->set_status(1);
		if ($cerate&&!empty($db)){
			return $this->user->create($db);
		}
		return $this->user;
	}
	function get_userOb(){
		return $this->user;
	}
	function name($name){
		if (validate::name($name,3,30)){
			$names=explode(" ", $name);
			$this->user->set_first_name(array_shift($names));
			if (isset($names[0])){
				$this->user->set_last_name(implode(" ", $names));
			}
		}else {
			$this->error['name']="Invalid Name";
		}
		
	}
	function email($email){
		if (validate::email($email)){
			$this->user->set_email($email);
		}else{
			$this->error['email']="Invalid Email";
		}
	}
	function dob($date){
		$this->user->set_dob($date);
	}
	function password($password){
		if (validate::password($password)){
			$this->user->set_password($password);
		}else{
			$this->error['password']="Invalid Password";
		}
	}
	function gender($gender){
		$this->user->set_gender($gender);
	}
}