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
class Message{
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
	 * first get recent message id
	 * then latest messages of them
	 */
	static function get_recent_message_list($user_id,$db,$start=0,$no_of_results=10){
		
		$start=is_numeric($start)?$start:0;
		$no_of_results=is_numeric($no_of_results)?$no_of_results:10;
		/*
		$inner_sql="SELECT DISTINCT CASE
			WHEN `user_one` = '$user_id' THEN `user_two`
			ELSE `user_one`
			END AS msg from `".self::$table."` where `user_one` = '$user_id' OR `user_two` = '$user_id'";
		$sql=Db::create_sql('*', self::$table,
				"((`user_one` = '$user_id' AND `user_two` IN($inner_sql))  OR
				(`user_one` IN($inner_sql) AND `user_two` = '$user_id')) GROUP BY 
				CASE
			WHEN `user_one` = '$user_id' THEN `user_two`
			ELSE `user_one`
			END ORDER BY `message_id` DESC");
			*/
		
		/*
		 * Query to get message Id.
		 */
		$innersql = Db::create_sql('max(`message_id`)',self::$table,
		"`user_one` = '$user_id' OR `user_two` = '$user_id'",
		null,
		"CASE
			WHEN `user_one` = '$user_id' THEN `user_two`
			ELSE `user_one`
			END");
		
		$sql=Db::create_sql(array(
				"first_name",
				"last_name",
				"users.user_id",
				"message",
				"time",
				"messages.status"
		), array(
				"users",
				self::$table
		),
				"`message_id` IN($innersql) AND
				`users`.`user_id`= CASE
			WHEN `user_one` = '$user_id' THEN `user_two`
			ELSE `user_one`
			END",
				
			"`message_id` DESC",
				
			null,
			"$start,$no_of_results"
		);
// 		echo $sql;
		return Db::fetch_array($db, $sql);
	}
	static function get_unread_count($user_id,$db){
		$sql=Db::create_sql('count(DISTINCT user_one)"count"', self::$table,
				"`user_two`='$user_id' AND status IN(1,2)"
			);
		if (empty($db)){
			return $sql;
		}else{
			$result=Db::qnfetch($sql, $db);
			return $result['count'];
		}
	}
	private static function set_status($message_id,$status,$db){
		$sql=Db::create_update_sql($db, self::$table, array(
				"status"=>$status
		), array("message_id"=>$message_id));
		return $db->query($sql)?$db->affected_rows():false;
		
	}
	static function mark_received($message_id,$db){
		return self::set_status($message_id, 2, $db);
	}
	static function mark_read($message_id,$db){
		return self::set_status($message_id, 3, $db);
	}
	/*
	 * Get all the messages between 2 users.
	 * @user_one-logged in user
	 * user_two-his friend
	 * start,no_of_results-for navigation
	 * 
	 * @after-for getting new messages useful for ajax.
	 */
	static function get_messages($user_one,$user_two,$db,$start=NULL,$no_of_results=NULL,$after=NULL,$sortDesc=TRUE){
		
		$start=is_numeric($start)?$start:0;
		$no_of_results=is_numeric($no_of_results)?$no_of_results:10;
		
		if (is_numeric($after)){
			$after=" AND `message_id`>$after ";
		}
		$sort=$sortDesc?"`message_id` DESC":"`message_id`";
		$sql=Db::create_sql('*', self::$table,"
				((`user_one`='$user_two' AND `user_two`='$user_one') OR 
				(`user_one`='$user_one' AND `user_two`='$user_two')) $after",
				$sort,
				null,
				"$start,$no_of_results");
		return Db::fetch_array($db, $sql);
	}
	static function get_chat_list($user_id,$db,$start=0,$limit=15){
		$friend_list=Friendship::get_friend_ids($user_id, null);
		$sql=Db::create_sql(array(
				"first_name",
				"last_name",
				"users.user_id",
				"data"
		), array(
				"userdata",
				"users"
		),
				"`userdata`.`user_id` IN($friend_list) AND
				`users`.`user_id`=`userdata`.`user_id` AND
				`userdata`.`type`='lastactive' ",
				"data DESC",
				null,
				"$start,$limit"
		);
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
}