<?php 
class Cookies {
	static function verify_cookies($id, $token,$db) {
		if ($id == null) {
			$id = isset ( $_COOKIE ['user_id'] ) ? $_COOKIE ['user_id'] : null;
			if ($id == null) {
				return false;
			}
		}
		$id=(int)$id;
		
		if ($token == null) {
			$token = isset ( $_COOKIE ['token'] ) ? $_COOKIE ['token'] : null;
			if ($token == null) {
				return false;
			}
		}
		
		$data=Keys::get_key_data($id, $token, $db);
		
		if (!empty($data['exp'])){
			return Keys::is_valid($data['exp'])?$id:false;
		}else {
			return false;
		}
		
	}
	static function create_cookie($user_id,$db,$time=NULL){
		if ($time == null) {
			$time = "1 months";
		}
		$key=Keys::gen_key($user_id, $time, $db);
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
	
}