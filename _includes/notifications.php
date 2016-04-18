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
 * 3-Sent you friend request
 * 4-Accepted friend request
 */
class Notifications{
	private static $table="notifications";
	private static $columns=array(
			"user_id",
			"from_user_id",
			"post_id",
			"type",
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
		if (isset($data['msg']) && !empty($data['msg'])){
			$sql=Db::create_sql_insert('notification_msg', array(
					"msg"=>$data['msg']
			),null,$db);
			if (!empty($db) && $db->query($sql)){
				$sqlData['type']=$db->last_insert_id();
			}else {
				return false;
			}
		}
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
    WHEN type=1 THEN (SELECT count(user_id) from likes AS l WHERE l.post_id=n.post_id AND l.type=1 AND l.user_id != n.user_id)
    WHEN type=2 THEN (SELECT count(DISTINCT c.user_id) FROM comments AS c WHERE c.post_id=n.post_id AND c.USER_ID != n.user_id)
    
    WHEN type NOT IN(1,2) THEN NULL
    END AS people_count,
    (SELECT `msg` FROM `notification_msg` as nm where nm.type=n.type) AS message,
    (SELECT SUBSTRING(`post_data`,1,25) from `post` where `post_id`=n.post_id) AS post_data,
    (SELECT `picture_id` from `post` where `post_id`=n.post_id) AS post_img,
    n.post_id,
    n.from_user_id,
    CASE
    	WHEN n.from_user_id IS NOT NULL THEN
    	(SELECT concat(`first_name` ,' ',IFNULL(`last_name`,'')) FROM `users` as u WHERE u.`user_id`=n.from_user_id)
    	WHEN n.from_user_id IS NULL THEN n.from_user_id
    END as from_user_name,
    n.type,
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
/*
 * Class to send notification mails to users
 */
class NotificationEmails{
	static function send_mail($email,$subject,$message,$from_name,$from_email,$reply_to=NULL){
		$headers = "From: $from_name $from_email\r\n";
		if (!empty($reply_to)){
			$headers .= "Reply-To: $reply_to\r\n";
		}
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		mail($email,$subject,$message,$headers);
	}
	static function send_password_recovery_email($user,$key){
		$user_email=$user->get_email();
		$user_id=$user->get_user_id();
		$user_name=$user->get("first_name");
		
		$host=$_SERVER["SERVER_NAME"];
		$reset_link="http://$host/password_recover.php?token=$key&user=$user_id";
		$valid_till=date('M d, Y \a\t h:i a',strtotime("+24 hours"));
		
		$message=<<<EOS
		Hey <b>$user_name</b>,<br>
		we heard you lost your password. Say it ain't so!
		Click <a href='$reset_link'>here</a> to make a new one.
				
		If you are unable to click above copy and paste below link in your address bar.
		
		$reset_link
		
		<i>*Above link is valid till $valid_till</i>
EOS;
		$message=nl2br($message);
		self::send_mail($user_email, "Make new Password", $message, "BeeHive", "noreply@$host");
	}
}