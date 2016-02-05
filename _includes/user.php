<?php
/**
 * This Class Handles basic details of user.
 * 
 * @status
 * 1-active
 * 2-deactivated.
 * 
 * Caution:A user table is a parent table So do not try to delete any existing user(You wont be able to)
 * rather deactivate him.
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
		$this->initialise_by_id($user_id,$db);
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
	function get_user_id(){
		return $this->get($this->pk);
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
	function activate_user(){
		$this->set_status(1);
	}
	function deactivate_user(){
		$this->set_status(2);
	}
	static function search_users_by_name($name,$db,$start=0,$limit=10){
		$name=strtolower($name);
		$sql=Db::create_sql(array(
				"user_id",
				"first_name",
				"last_name"
		), 
				'users',
				"lower(concat(`first_name`,`last_name`)) LIKE '%$name%' AND
				status=1",
				null,null,
				"$start,$limit");
		
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
	static function email_registered($email,$db){
		$semail=$db->escape($email);
		$sql=db::create_sql('count(user_id)num', 'users',"email='$semail'",null,null,1);
		$result= $db->qfetch($sql);
		return 1===(int)$result['num'];
	}
	static function update_last_active($user_id,$db){
		$time=strtotime("now");
		UserData::edit_by_type($user_id,"lastactive", strtotime("now"), $db);
	}
}
class UserData{
	private static  $table="userdata";
	static function add_data($user_id,$type,$data,$db){
		$sql=Db::create_sql_insert(self::$table, array(
				"user_id"=>$user_id,
				"type"=>$type,
				"data"=>$data
		),null,$db);
		return empty($db)?$sql:(Db::queryy($sql, $db)?Db::last_inserted_id($db):false);
	}
	static function get_user_data($user_id,$type,$db){
		$where=array(
						"user_id"=>$user_id,
						"status"=>1
				);
		if ($type!=null){
			$where["type"]=$type;
		}
		$sql=Db::create_sql(array(
				"data_id",
				"type",
				"data"
		), self::$table,
				$where);
		return empty($db)?$sql:db::fetch_array($db, $sql);
	}
	static function get_all_data($user_id,$db){
		return self::get_user_data($user_id, null, $db);
	}
	static function edit_data($data_id,$type,$data,$db,$status=NULL){
		$update_data=array();
		if (!empty($type)){
			$update_data['type']=$type;
		}
		if (!empty($data)){
			$update_data['data']=$data;
		}
		if ($status===0 || $status===1){
			$update_data['status']=$status;
		}
		if (empty($update_data)){
			return false;
		}
		$update_data['data_id']=$data_id;
		$sql=Db::create_update_sql($db, self::$table,$update_data , "data_id");
		return empty($db)?$sql:(Db::queryy($sql, $db)?Db::rows_affected($db):false);
	}
	static function edit_by_type($user_id,$type,$newdata,$db){
		$update_data['data']=$newdata;
		$pk['type']=$type;
		$pk['user_id']=$user_id;
		
		$sql=Db::create_update_sql($db, self::$table, $update_data, $pk);
		
		return empty($db)?$sql:(Db::queryy($sql, $db)?Db::rows_affected($db):false);
	}
	static function remove_data($data_id,$db){
		return self::edit_data($data_id, null, null, $db,0);
	}
	static function re_activate_data($data_id,$db){
		return self::edit_data($data_id, null, null, $db,1);
	}
}