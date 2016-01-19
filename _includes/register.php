<?php
class Register{
	private $error;
	private $data;
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
		foreach ($this->data as $key=>$val){
			$this->user->set($key, $val);
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
			$this->data['first_name']=array_shift($names);
			if (isset($names[0])){
				$this->data['last_name']=implode(" ", $names);
			}
		}else {
			$this->error['name']="Invalid Name";
		}
		
	}
	function email($email){
		if (validate::email($email)){
			$this->data['email']=$email;
		}else{
			$this->error['email']="Invalid Email";
		}
	}
	function dob($date){
		$this->data['dob']=$date;
	}
	function password($password){
		if (validate::password($password)){
			$password=$password;
		}else{
			$this->error['name']="Invalid Password";
		}
	}
	function gender($gender){
		$this->data['gender']=$gender;
	}
}