<?php
define('ENCRYPTING_CHARACTER_SET', "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
class password{
	static function create_password($password,$cost="09"){
		$salt=Keys::get_random_string(22);
		$key='$2a$'.$cost.'$'.$salt;
		return crypt($password,$key);
	}
	static function verify_password($password,$hash){
		return crypt($password,$hash)===$hash;
	}
}
class secure{
	static private $isOriginalSet=FALSE;//To avoide multiple calls.
	static function createChanged($passKey){
		/*
		 * Function to shuffle Set of characters with respect to an key.
		 */
		$normal = ENCRYPTING_CHARACTER_SET;
	
		$i=str_split($normal);

// 		$passhash = 64 < strlen($normal)? hash('sha512',$passKey): hash('sha256',$passKey);
		$passhash = 64 < hash((strlen($normal)?'sha512':'sha256'),$passKey);
	
		for ($n=0; $n < strlen($normal); $n++){
			$p[] =  substr($passhash, $n ,1);
		}
	
		array_multisort($p,  SORT_DESC, $i);
		$converted = implode($i);
	
		return $converted;
	}
	static function basicEncrypt($input,$key,$decrypt=FALSE,$salt='AnOptionalRandomString'){
		/*
		 * Function To do an basic encryption(basicaly for form elements.)
		 */
		$changedkey=self::createChanged($salt.$key);
	
		$normal = $decrypt?$changedkey:ENCRYPTING_CHARACTER_SET;
		$changed=$decrypt?ENCRYPTING_CHARACTER_SET:$changedkey;
	
		$output='';
		$n=str_split($input);
		$index=array();
	
		for($i=0;$i<strlen($normal);$i++){
			$index[substr($normal,$i,1)]=substr($changed,$i,1);
		}
		for ($i=0;$i<strlen($input);$i++){
			$output.=isset($index[substr($input,$i,1)])?$index[substr($input,$i,1)]:substr($input,$i,1);
		}
		return $output;
	}
	static function setOriginalElementNames(){
		if (self::$isOriginalSet||!isset($_SESSION['form']['key'])){
			return;
		}
		$keys=array_keys($_POST);
		foreach ($keys as $key){
			$_POST[self::basicEncrypt($key, $_SESSION['form']['key'],true)]=&$_POST[$key];
			// 		unset($_POST[$key]);//Removes Backup variable.s
		}
		self::$isOriginalSet=true;
	}
	static function encrypt_form_name($name){
		return self::basicEncrypt($name, $_SESSION['form']['key']);
	}
	static function decrypt_form_name($name){
		return self::basicEncrypt($name, $_SESSION['form']['key'],true);
	}
	static function get_random_string($min=NULL,$max=NULL){
		$min=$min==NULL?rand(2,9):$min;//Default range is between 2 and 9 change this if needed.
		$max=$max==NULL?$min:$max;
	
		$str="";
		while (strlen($str)<$max)
			$str.=rtrim(base64_encode(hash("sha512",microtime())),"=");
		#$str=str_shuffle($str);//Optional Code as the generated string is random of itself.
		return substr($str, 0, rand($min, $max));
	}
	static function change_key($form_expiry="5 mins"){
		$_SESSION['form']['key']=Keys::get_random_string(5,11);
		$_SESSION['form']['time']=strtotime("+ $form_expiry");
	}
}
class validate{
	static function email($email){
// 		echo filter_var($email,FILTER_VALIDATE_EMAIL)?"":$email;
		return filter_var($email,FILTER_VALIDATE_EMAIL);
	}
	static function userName($userName){
// 		echo preg_match('/\A[a-z][a-zA-Z\d_]{3,10}\Z/m', $userName)?"":$userName;
		return preg_match('/\A[a-z][a-zA-Z\d_]{3,10}\Z/m', $userName);
	}
	static function name($name,$min=3,$max=10){
// 		echo preg_match('/\A[a-zA-Z.]{3,10}\Z/m', $name)?"":$name;
		return preg_match('/\A[a-zA-Z. ]{'.$min.','.$max.'}\Z/m', $name);
	}
	static function password($password){
// 		echo preg_match('%(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!-/:-@{-~[-`]).{8,35}%m', $password)?"":"$password";
		return preg_match('%(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!-/:-@{-~[-`]).{8,35}%m', $password);
	}
	static function number($number,$length=null){
// 		echo preg_match('/\A\d{'.$length.'}\Z/m', $number)?"":"$number";

		$repetation=$length==null?"+":"\{$length\}";
		return preg_match('/\A\d'.$repetation.'\Z/m', $number);
	}
}
class Keys{
	static function gen_key($user_id,$validity,$db){
		$key=self::get_random_string(30,60);
		$exp=strtotime("+ $validity");
		
		$data=array(
				"user_id"=>$user_id,
				"skey"=>$key,
				"exp"=>$exp
		);
		$sql=Db::create_sql_insert('keys', $data);
		return $db->query($sql)?$key:false;
	}
	static function get_key_data($user_id,$key,$db,$hit_limit=null){
		/*
		 * Escaping
		 */
		$skey=$db->escape($key);
		$suser_id=$db->escape($user_id);
		
		$where="user_id='$suser_id' AND skey='$skey' AND status= 1";
		
		$where.=$hit_limit==null?"":" AND hits < '$hit_limit'";
		
		$sql=Db::create_sql(array('exp','lastused','hits'), array('keys'),$where);
		return $db->qfetch($sql);
	}
	static function is_valid($unix_time){
		return strtotime('now')<$unix_time;
	}
	static function get_random_string($min=NULL,$max=NULL){
		$min=$min==NULL?rand(2,9):$min;//Default range is between 2 and 9 change this if needed.
		$max=$max==NULL?$min:$max;
	
		$str="";
		while (strlen($str)<$max)
			$str.=rtrim(base64_encode(hash("sha512",microtime())),"=");
		#$str=str_shuffle($str);//Optional Code as the generated string is random of itself.
		return substr($str, 0, rand($min, $max));
	}
}