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
	private function search_user_by($column,$data,$db,$start=0,$limit=10){
		if (empty($column) || strlen($data)<3){
			return null;
		}
		$data=strtolower($data);
		$sql=Db::create_sql(array(
				"user_id",
				"first_name",
				"last_name"
		),
				'users',
				"$column LIKE '%$data%' AND
				status=1",
				null,null,
				"$start,$limit");
				return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
	static function search_users_by_name($name,$db,$start=0,$limit=10){
		if (validate::email($name)){//if user directly puts email in searchbox
			return self::search_user_by("email", $name, $db,$start=0,$limit=10);
		}
		return self::search_user_by("lower(concat(`first_name`,`last_name`))",
				$name, $db,$start=0,$limit=10);
	}
	static function search_by_userdata($type,$data,$db){
		$data=strtolower($data);
		$sql=Db::create_sql(array(
				"first_name",
				"last_name",
				"users.user_id"
		), array(
				"userdata",
				"users"
		),
				"`users`.`user_id`=`userdata`.`user_id` AND
				`type`='$type' AND 
				lower(`data`) LIKE '%$data%' AND 
				`userdata`.`status`=1 "
		);
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
	static function search($text,$db,$start=0,$limit=15){
		global $user_search_list,$user_data_search_list;
		if (0<$pos=strpos($text, ":")){//Advance search
			
			$key_word= substr($text, 0,$pos);//getting key
			$data=substr($text, $pos+1); //Getting search text
			if (strlen(trim($data))<3){
				return null;
			}
			if (in_array($key_word, $user_search_list)){//If we need to lookup in user table only
				if ($key_word=="email" && validate::email($data)){
					return self::search_user_by("email", trim($data), $db);
				}else {
					return self::search_users_by_name(trim($data), $db);
				}
			}elseif (in_array(ucwords(strtolower(trim($key_word))), $user_data_search_list)){//We need to look on userdata table.
				return self::search_by_userdata(ucwords(strtolower(trim($key_word))), trim($data), $db);
			}else {
				return self::search_users_by_name(trim($data), $db);//Lets search by name only instead.
			}
		}else {//Search By Name
			return self::search_users_by_name($text, $db);
		}
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
	static function get_user_data($user_id,$type,$db,$rows=NULL){
		if (empty($rows)){
			$rows=array(
				"data_id",
				"type",
				"data"
			);
		}
		
		$where="`user_id`='$user_id' AND `status`=1 ";
		
		if (is_array($type)){
			$typelist=implode("','", $type);
			$where.="AND `type` IN('$typelist')";
		}elseif($type!=null) {
				$where.=" AND `type` = '$type' ";
		}
		
		$sql=Db::create_sql($rows, self::$table,
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
	static function insert_multiple($data,$db){
		$sql=Db::create_sql_insert_multiple(self::$table, $data,array("data","status"),$db);
		return empty($db)?$sql:(Db::queryy($sql, $db)?Db::rows_affected($db):false); 
	}
	static function remove_data($data_id,$db){
		return self::edit_data($data_id, null, null, $db,0);
	}
	static function re_activate_data($data_id,$db){
		return self::edit_data($data_id, null, null, $db,1);
	}
}