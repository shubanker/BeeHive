<?php
/*
 * This Class Handles basic details of user.
 */
class User extends Struct{
	
	function __construct($user_id=NULL,$db=null){

		$this->table="users";
		$this->pk="user_id";
		$this->data=array();
		
		$this->fields=array(
				"user_id",
				"first_name",
				"last_name",
				"email",
				"password",
				"gender",
				"dob",
				"status"
		);
		if (!empty($user_id) && !empty($db)){
			$this->initialise_by_id($user_id,$db);
			
		}else {
			foreach ($this->fields as $field){
				$this->data[$field]=null;//Seting default value to null for all fields.
			}
		}
	}
	function initialise_by_email($db,$email){
		$semail=$db->escape($email);
		$sql=Db::create_sql('*', array('users'),"email='$semail' AND status=1");
		$result=Db::fetch_array($db, $sql);
	
		if (count($result)==1){
			foreach ($this->fields as $field){
				$this->data[$field]=$result[0][$field];
			}
			$this->data[$this->pk]=(int)$this->data[$this->pk];
			
// 			$this->initialise_user_data($db);
			
			return $this->is_init=true;
		}else {
			return false;
		}
	
	}
	function get_name(){
		return $this->get("first_name")." ".$this->get("last_name");
	}
	function set_first_name($first_name){
		$this->set("first_name", $first_name);
	}
	function set_last_name($last_name){
		$this->set("last_name", $last_name);
	}
	function set_email($email){
		$this->set("email", $email);
	}
	function get_email(){
		return $this->get("email");
	}
	function set_password($password){
		$this->set("password", password::create_password($password));
	}
	function check_password($password){
		return password::verify_password($password, $this->get("password"));
	}
	function set_dob($date){
		$this->set("dob", date("Y-m-d",strtotime($date)));
	}
	function get_dob(){
		return $this->get("dob");
	}
	function set_gender($gen){
		$this->set("gender", strtoupper(substr($gen, 0,1))=="F"?"F":"M");//Default Value is M for male.
	}
	function get_gen(){
		return $this->get("gender");
	}
	function set_status($status){
		$this->set("status", $status);
	}
	function get_status(){
		return $this->get("status");
	}
}