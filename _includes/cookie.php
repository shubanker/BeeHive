<?php 
class Cookies {
	static function verify_cookies($user_id, $token,$db) {
		if ($user_id == null) {
			$user_id = isset ( $_COOKIE ['user_id'] ) ? $_COOKIE ['user_id'] : null;
		}
		$user_id=(int)$user_id;
		
		if ($token == null) {
			$token = isset ( $_COOKIE ['token'] ) ? $_COOKIE ['token'] : null;
		}
		if (empty($user_id)||empty($token)){
			return false;
		}
		$data=Keys::get_key_data($user_id, $token, $db);
		
		if (!empty($data['exp'])){
			Keys::update_hits($user_id, $token, $db);
			return Keys::is_valid($data['exp'])?$user_id:false;
		}else {
			return false;
		}
		
	}
	static function create_cookie($user_id,$db,$time=NULL){
		if ($time == null) {
			$time = "1 months";
		}
		$key=Keys::gen_key($user_id, $time, $db,true);
		$unix_time=strtotime("+ $time");
		setcookie('user_id',$user_id,$unix_time);
		setcookie('token',$key,$unix_time);
	}
	static function deactivate_cookie($user_id,$token,$db){
		$data=array(
				"status"=>2
		);
		db::delete($db, 'keys', array(
			"user_id"=>$user_id,
			"skey"=>$token
		));
		$time = strtotime ( "-3 months" );
		if (isset($_COOKIE['user_id'])){
			setcookie ( "user_id", "", $time );
		}
		if (isset($_COOKIE['token']))
			setcookie ( "token", "", $time );
		
	}
	static function clear_logins($user_id,$sessions,$db){
		$active_token=isset($_COOKIE['token'])?trim($_COOKIE['token']):"";
		$active_token=Db::escapee($active_token);
		$where_data="`user_id`='$user_id' AND ";
		
		if (empty($sessions)|| $sessions == 'all'){
			$where_data.="`skey` !='$active_token'";
		}else {
			$where_data.="`skey` IN('".implode("','", $sessions)."')";
		}
		$sql=Db::create_sql_delete('keys', $where_data);
		return empty($db)?$sql:($db->query($sql)?$db->affected_rows():false);
	}
	static function active_logins_count($user_id,$db,$my_key=''){
		$sql=Db::create_sql('count(*)count', array('keys'),
				"`user_id` ='$user_id' AND 
				`lastused` IS NOT NULL AND
				`skey` != '$my_key'
				"
			);
		if (empty($db)){
			return $sql;
		}elseif ($result=Db::qnfetch($sql, $db)){
			return $result['count'];
		}else {
			return false;
		}
		return empty($db)?$sql:Db::qnfetch($sql, $db);
	}
	
}