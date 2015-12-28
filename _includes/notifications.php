<?php
/**
 * Manages Notification for a user.
 * 
 * @status
 * 1-not seen
 * 2-seen
 * 
 * @type
 * 1-likes
 * 2-comments
 * 3-contains special msg
 */
class Notifications{
	private static $table="notifications";
	
	static function add_notification($user_id,$post_id,$type,$msg=null,$db=NULL){
		$sql=Db::create_sql_insert(self::$table, array(
				"user_id"=>$user_id,
				"post_id"=>$post_id,
				"type"=>$type,
				"msg"=>$msg,
				"status"=>1
		),null,$db,array(
				"status",
				"time"
		));
		return empty($db)?$sql:($db->query($sql)?$db->last_insert_id():false);
	}
	static private function update_status($notification_id,$status,$db=NULL){
		$sql=Db::create_update_sql($db, self::$table, array(
				"status"=>$status,
				"notification_id"=>$notification_id
		), "notification_id");
		return empty($db)?$sql:($db->query($sql)?$db->affected_rows():false);
	}
	static function mark_read($notification_id,$db){
		return self::update_status($notification_id, 0,$db);
	}
	static function mark_unread($notification_id,$db){
		return self::update_status($notification_id, 1,$db);
	}
	static function get_notification_count($user_id,$db=null){
		$sql= Db::create_sql('count(`notification_id`)', self::$table,
				"`user_id`='$user_id' AND status=1");
		return empty($db)?$sql:(is_numeric($user_id)?Db::qnfetch($sql, $db):false);
	}
	/*
	 * @status
	 * 0-read
	 * 1-unread
	 * null-all
	 */
	static function get_notifications($user_id,$status=NULL,$db=NULL){
		$status_condition = $status===0||$status===1?"AND `status`='$status'":"";
		$sql="SELECT
		`notification_id`,
				CASE 
				WHEN `msg` IS NOT NULL THEN msg
				WHEN `type`=1 THEN concat((SELECT count(user_id) from `likes` WHERE `post_id`=post_id AND `type`=1 AND `user_id`!='$user_id'),' peoples Liked your post')
				WHEN `type`=2 THEN concat((SELECT count(*) from (SELECT DISTINCT `user_id` FROM `comments` WHERE `post_id`=post_id AND `user_id`!='$user_id') AS commenters),' peoples Commented on your post')
				END AS message
				FROM
				`notifications` WHERE user_id='$user_id' $status_condition";
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
}