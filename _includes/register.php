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
			if($user_id=$this->user->create($db)){
				self::add_essential_userdata($user_id, $db);
				return $user_id;
			}
		}
		return $this->user;
	}
	private static function get_defaults($type){
		switch ($type){
			case 'Country':return "India";
			default:return null;
		}
	}
	static function add_essential_userdata($user_id,$db){
		$types=array("dp","lastactive","Country");
		$data=array();
		foreach ($types as $type){
			$data[]=array(
					"user_id"=>$user_id,
					"type"=>$type,
					"data"=>self::get_defaults($type)
			);
		}
		$sql=db::create_sql_insert_multiple("userdata", $data);
		return empty($db)?$sql:$db->query($sql);
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