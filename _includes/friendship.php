<?php
/*
 * Class Handles relations between users
 * 
 * 
 * Databse:
 * table "friends"
 * Colums: friend_id,user_one,user_two,status
 * 
 * user_one one who makes a request
 * user_two the person whome request has been sent.
 * status value desides the relation as follows:
 * 1-friend req sent by user_one to user_two
 * 2-req accepted.
 * 3-user blocked.
 * 0-req denied/unfriend/unblocked (can even delete the row in such case)
 * 
 * 
 */
class Friendship{
	
	private static $table="friends";
	
	static function get_friend_ids($user_id,$db){
		$sql="SELECT
		CASE
		WHEN `user_one` = '$user_id' THEN `user_two`
		ELSE `user_one`
		END AS FRIENDS
		FROM `friends` WHERE (`user_one`='$user_id' OR `user_two`='$user_id') AND status=2 ";
		if (empty($db)){
			return $sql;
		}
		$list=array();
		foreach (Db::fetch_array($db, $sql) as $friend){
			$list[]=$friend['FRIENDS'];
		}
		return $list;
	}
	static function get_all_connections($user_id,$db,$include_bloced=FALSE){
		$status_values=array(1,2);
		if ($include_bloced){
			$status_values[]=3;
		}
		$status_condition="AND `friends`.`status` IN(".(implode(",", $status_values)).")";
		$sql="SELECT
		CASE
			WHEN `user_one` = '$user_id' THEN `user_two`
			ELSE `user_one`
		END AS connected_user,
		CASE
			WHEN `friends`.`status`=2 THEN 'friends'
			WHEN `friends`.`status`=1 AND `user_one` = '$user_id' THEN 'following'
			WHEN `friends`.`status`=1 AND `user_two` = '$user_id' THEN 'followers'
			WHEN `friends`.`status`=3 AND `user_one` = '$user_id' THEN 'blocked'
			WHEN `friends`.`status`=3 AND `user_two` = '$user_id' THEN 'blockedby'
		END AS connection,
        concat(`first_name` ,' ',IFNULL(`last_name`,'')) as name
		FROM `friends`,`users` WHERE
			(`user_one`='$user_id' OR `user_two`='$user_id') AND 
			((`user_two`='$user_id' AND `user_one`=`users`.`user_id`) OR (`user_one`='$user_id' AND `user_two`=`users`.`user_id`)) $status_condition";
		if (empty($db)){
			return $sql;
		}
		return Db::fetch_array($db, $sql);
	}
	static function get_following_ids($user_id,$db){
		$sql=Db::create_sql(array('user_two'), array('friends'),
				array(
						"user_one"=>$user_id,
						"status"=>1
				)
			);
		if (empty($db)){
			return $sql;
		}
		$list=array();
		foreach (Db::fetch_array($db, $sql) as $friend){
			$list[]=$friend['user_two'];
		}
		return $list;
	}
	static function get_friends_names($user_id,$db){
		$sql="SELECT `user_id`,concat(`first_name` ,' ',IFNULL(`last_name`,'')) as name FROM `users` WHERE `user_id` IN(SELECT
		CASE
		WHEN `user_one` = '$user_id' THEN `user_two`
		ELSE `user_one`
		END AS FRIENDS
		FROM `friends` WHERE (`user_one`='$user_id' OR `user_two`='$user_id') AND status=2 );";

		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
	static function get_friend_count($user_id,$db){
		$sql=Db::create_sql("count(*)count", self::$table,"(`user_one`='$user_id' OR `user_two`='$user_id') AND status=2");
		
		return empty($db)?$sql:Db::qnfetch($sql, $db)['count'];
	}
	static function get_mutuals($user_one,$user_two,$db,$start=NULL,$limit=NULL){
		$friend_code=2;//status code of friendship
		if (!empty($limit)){
			if (empty($start)){
				$start=0;
			}
			$limit_sql="LIMIT $start,$limit";
		}else {
			$limit_sql="";
		}
		$sql="
		SELECT `user_id`,concat(`first_name` ,' ',IFNULL(`last_name`,'')) as name FROM `users` WHERE `user_id` IN(
			SELECT
			CASE
			WHEN `user_one` = '$user_one' THEN `user_two`
			ELSE `user_one`
			END AS FRIENDS
			FROM `friends`
			WHERE (`user_one`='$user_one' OR `user_two`='$user_one') AND 
			status=$friend_code AND
			`user_id` IN (
				SELECT
				CASE
				WHEN `user_one` = '$user_two' THEN `user_two`
				ELSE `user_one`
				END AS FRIENDS
				FROM `friends`
				WHERE (`user_one`='$user_two' OR `user_two`='$user_two') AND
				status=$friend_code
				)
	) $limit_sql;";
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
	static function send_friend_req($user_one,$user_two,$db){
		/*
		 * Doing it the wrong way by finding if their row exist and isn't blocked etc and then sending request if possible
		 * Will update in one query in future If I can.
		 * 
		 * Todo:check if both user exist's in first place, a security feature left because no one cares.
		 */
		$sql=Db::create_sql('count(*)count', array(self::$table),
				"((`user_one`='$user_two' AND `user_two`='$user_one') OR 
				(`user_one`='$user_one' AND `user_two`='$user_two')) AND status>0"
				);
		if (Db::qnfetch($sql, $db)['count']>0){
			return false;//Friendship can not be sent because either both are already friends or user is blocked.
		}
		$sql=Db::create_sql_insert(self::$table, array(
				"user_one"=>$user_one,
				"user_two"=>$user_two,
				"status"=>1,
		),null,$db);
		return $db->query($sql)?$db->last_insert_id():false;
	}
	/*
	 * A friendreq is genuine if user_one sends req to uer_two and its status is 2
	 */
	static function accept_request($user_one,$user_two,$db){
		$sql=Db::create_update_sql($db, 'friends', array(
				"status"=>2
		), array(
				"user_one"=>$user_one,
				"user_two"=>$user_two,
				"status"=>1
		),true);
		return $db->query($sql)?$db->affected_rows():false;
	}
	static function cancle_friend_req($user_one,$user_two,$db){
		$sql=Db::create_sql_delete('friends', array(
				"user_one"=>$user_one,
				"user_two"=>$user_two,
				"status"=>1
		));
		return $db->query($sql)?$db->affected_rows():false;
	}
	static function unfriend($user_one,$user_two,$db){
		$sql=Db::create_sql_delete('friends', "((`user_one`='$user_two' AND `user_two`='$user_one') OR 
				(`user_one`='$user_one' AND `user_two`='$user_two')) AND status=2");
		return $db->query($sql)?$db->affected_rows():false;
	}
	/*
	 * Since once a user blocks all existing relations between them is over.
	 * so instead of checking existance of any just remove all relations and add a blocked one.
	 */
	static function block($user_one,$user_two,$db){
		$sql=Db::create_sql_delete(self::$table, "(`user_one`='$user_two' AND `user_two`='$user_one') OR 
				(`user_one`='$user_one' AND `user_two`='$user_two')");
		$sql.=";";
		if (!$db->query($sql)){
			return false;
		}
		$sql=Db::create_sql_insert(self::$table, array(
				"user_one"=>$user_one,
				"user_two"=>$user_two,
				"status"=>3,
		),null,$db);
		return $db->query($sql)?$db->affected_rows():false;
	}
	static function unblock($user_one,$user_two,$db){
		$sql=DB::create_sql_delete(self::$table, array(
				"user_one"=>$user_one,
				"user_two"=>$user_two,
				"status"=>3,
		));
		return $db->query($sql)?$db->affected_rows():false;
	}
	static function get_relation($user_one,$user_two,$db,$only_status=TRUE){
		$sql=Db::create_sql(array('status','user_one','user_two'), self::$table,"(`user_one`='$user_two' AND `user_two`='$user_one') OR 
				(`user_one`='$user_one' AND `user_two`='$user_two')");
		if (empty($db)){
			return $sql;
		}
		$result= $db->qnfetch($sql, $db);
		if (empty($result)){
			return 0;
		}else {
			return $only_status?$result['status']:$result;
		}
	}
	static function is_friend($user_one,$user_two,$db){
		return self::get_relation($user_one, $user_two, $db)==2;
	}
	static function get_action($user_id, $friend_id, $db,$relation){
		
		
		if ($relation!=0){
			$relation_status=$relation['status'];
			switch ($relation['status']){
				case 1:
					if ($relation['user_one']==$user_id){
						$friend_button="Cancle Request";
					}else {
						$friend_button="Accept Request";
					}
					break;
				case 2:
					$friend_button="Unfriend";
					break;
				case 0:
					$friend_button="Add Friend";
					break;
				case 3://Blocked Users..
					redirect_to();
					closendie();
					break;
			}
		}else{
			$relation_status=0;
			$friend_button="Add Friend";
		}
		return $friend_button;
	}
	static function suggested_friends($user_id,$db,$start=0,$limit=15){
		global $About_data_list;
		$friend_list=Friendship::get_friend_ids($user_id, null);
		$type_list=implode("','", $About_data_list);
		$user_data_sql=UserData::get_user_data($user_id, $About_data_list, null,array('data'));
		$sql="SELECT  FROM `userdata` WHERE 
			";
		$sql=Db::create_sql('DISTINCT `user_id`', array('userdata'),"
			`type` IN('$type_list') AND
			`user_id` NOT IN($friend_list) AND
			`user_id`!='$user_id' AND
			`data` IN($user_data_sql)"
			);
		$sql=Db::create_sql("`user_id`,concat(`first_name` ,' ',IFNULL(`last_name`,'')) as name", 
				array('users'),
				"`user_id` IN($sql)",
				null,null,
				"$start,$limit"
			);
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
}