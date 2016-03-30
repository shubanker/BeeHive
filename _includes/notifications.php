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
	private static $columns=array(
			"user_id",
			"from_user_id",
			"post_id",
			"type",
			"msg",
			"status"
	);
	static function add_notification($user_id,$data=array(),$db=NULL){
		$sqlData=array();
		foreach (self::$columns as $column){
			$sqlData[$column]=isset($data[$column])?$data[$column]:null;
		}
		$sqlData['user_id']=$user_id;
		if (empty($sqlData['user_id'])){
			return false;
		}
		$sqlData['status']=empty($sqlData['status'])?1:(int)$sqlData['status'];
		
		$sql=Db::create_sql_insert(self::$table, $sqlData,null,$db,array(
				"status",
				"time"
		));
		return empty($db)?$sql:($db->query($sql)?$db->last_insert_id():false);
	}
	static private function update_status($notification_id,$status,$db){
// 		$sql=Db::create_update_sql($db, self::$table, array(
// 				"status"=>$status,
// 				"notification_id"=>$notification_id
// 		), "notification_id");
		if (is_array($notification_id)){
			for ($i = 0; $i < count($notification_id); $i++) {
				$notification_id[$i]=$db->escape($notification_id[$i]);
				$where="notification_id IN('".implode("','", $notification_id)."')";
			}
		}else {
			$notification_id=$db->escape($notification_id);
			$where="notification_id='$notification_id'";
		}
		$sql=Db::create_update_sql2(self::$table, array("status"=>$status), $where, $db);
		return empty($db)?$sql:($db->query($sql)?$db->affected_rows():false);
	}
	static function mark_read($notification_id,$db){
		return self::update_status($notification_id, 0,$db);
	}
	static function mark_unread($notification_id,$db){
		return self::update_status($notification_id, 1,$db);
	}
	static function get_notification_count($user_id,$db=null){
		$sql= Db::create_sql('count(`notification_id`)count', self::$table,
				"`user_id`='$user_id' AND status=1");
		if (empty($db)){
			return $sql;
		}else {
			$result=Db::qnfetch($sql, $db);
			return $result['count'];
		}
	}
	/*
	 * @status
	 * 0-read
	 * 1-unread
	 * null-all
	 */
	static function get_notifications($user_id,$status=NULL,$db=NULL,$start=NULL,$limit=NULL,$before=NULL){
		$status_condition = $status===0||$status===1?"AND `status`='$status'":"";
		
		$start=$start==null?0:$start;
		$limit=$limit==null?10:$limit;
		$limit_sql=" LIMIT $start,$limit";
		
		$before_sql=empty($before)?"":" AND notification_id<'$before' ";
		/*$sql="SELECT
		`notification_id`,
				CASE 
				WHEN `msg` IS NOT NULL THEN msg
				WHEN `type`=1 THEN concat((SELECT count(user_id) from `likes` WHERE `post_id`=post_id AND `type`=1 AND `user_id`!='$user_id'),' peoples Liked your post')
				WHEN `type`=2 THEN concat((SELECT count(*) from (SELECT DISTINCT `user_id` FROM `comments` WHERE `post_id`=post_id AND `user_id`!='$user_id') AS commenters),' peoples Commented on your post')
				END AS message
				FROM
				`notifications` WHERE user_id='$user_id' $status_condition";*/
		$sql="SELECT n.notification_id,
    CASE 
    WHEN n.msg IS NOT NULL THEN n.msg
    WHEN type=1 THEN concat((SELECT count(user_id) from likes AS l WHERE l.post_id=n.post_id AND l.type=1 AND l.user_id != n.user_id),' person have Liked your ')
    WHEN type=2 THEN concat((SELECT count(DISTINCT c.user_id) FROM comments AS c WHERE c.post_id=n.post_id AND c.USER_ID != n.user_id),' person have Commented on your ')
    END AS message,
    (SELECT SUBSTRING(`post_data`,1,25) from `post` where `post_id`=n.post_id) AS post_data,
    (SELECT `picture_id` from `post` where `post_id`=n.post_id) AS post_img,
    n.post_id,
    n.from_user_id,
    CASE
    	WHEN n.from_user_id IS NOT NULL THEN
    	(SELECT concat(`first_name` ,' ',IFNULL(`last_name`,'')) FROM `users` as u WHERE u.`user_id`=n.from_user_id)
    	WHEN n.from_user_id IS NULL THEN n.from_user_id
    END as from_user_name,
    n.time,
    n.status
FROM
    notifications AS n 
WHERE 
    n.user_id='$user_id' $status_condition";
		$sql.=$before_sql;
		$sql.="ORDER BY n.time DESC";
		$sql.=$limit_sql;
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
}