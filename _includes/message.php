<?php
/*
 * Database:
 * table:
 * messages
 * message_id,user_one,user_two,status,time,message
 * 
 * status decides state of msg (useful for app maybe)
 * 
 * 1-sent
 * 2-received
 * 3-read
 * 0-deleted(can delete row as well)
 * 
 */
class message{
	private static $table="messages";
	static function send_message($user_one,$user_two,$message,$db){
		$sql=Db::create_sql_insert(self::$table, array(
				"user_one"=>$user_one,
				"user_two"=>$user_two,
				"message"=>$message,
		),null,$db);
		return $db->query($sql)?$db->last_insert_id():false;
	}
	/*
	 * Function returns list of recent messages..
	 * useful for displaying in message homepage.
	 * 
	 * recepie:
	 * first get recent contacts id
	 * then latest messages of them
	 */
	static function get_message_list($user_id,$db,$start=0,$no_of_results=10){
		$sql="SELECT DISTINCT CASE
			WHEN `user_one` = '$user_id' THEN `user_two`
			ELSE `user_one`
			END AS msg from `".self::$table."` where `user_one` = '$user_id' OR `user_two` = '$user_id' ORDER BY `message_id` DESC";
		
	}
	private static function set_status($message_id,$status,$db){
		echo $sql=Db::create_update_sql($db, self::$table, array(
				"status"=>$status
		), array("message_id"=>$message_id));
		
	}
	static function mark_received($message_id,$db){
		self::set_status($message_id, 2, $db);
	}
	static function mark_read($message_id,$db){
		self::set_status($message_id, 3, $db);
	}
}