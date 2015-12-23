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
	
	private $user_id;
	private $friend_list;
	
	function __construct($user_id=null,$db=null){
		if ($user_id!=null){
			$this->user_id=$user_id;
		}
		if($user_id!=null&&$db!=null){
			
		}
	}
	function initialise_by_userid($user_id,$db){
		$sql="SELECT 
				CASE 
				WHEN `user_one` = '$user_id' THEN `user_two`
				ELSE `user_one`
				END AS FRIENDS,status
				FROM `friends` WHERE (`user_one`='$user_id' OR `user_two`='$user_id') AND status=2 ;";
		$data=Db::fetch_array($db, $sql);
	}
	static function get_friend_ids($user_id,$db){
		$sql="SELECT
		CASE
		WHEN `user_one` = '$user_id' THEN `user_two`
		ELSE `user_one`
		END AS FRIENDS
		FROM `friends` WHERE (`user_one`='$user_id' OR `user_two`='$user_id') AND status=2 ;";
		$list=array();
		foreach (Db::fetch_array($db, $sql) as $friend){
			$list[]=$friend['FRIENDS'];
		}
		return $list;
	}
	static function get_friends_names($user_id,$db){
		$sql="SELECT `user_id`,concat(`first_name` ,' ',`last_name`) as name FROM `users` WHERE `user_id` IN(SELECT
		CASE
		WHEN `user_one` = '$user_id' THEN `user_two`
		ELSE `user_one`
		END AS FRIENDS
		FROM `friends` WHERE (`user_one`='$user_id' OR `user_two`='$user_id') AND status=2 );";
		return Db::fetch_array($db, $sql);
	}
	static function get_friend_count($user_id,$db){
		echo $sql=Db::create_sql("count(*)count", 'friends',"(`user_one`='$user_id' OR `user_two`='$user_id') AND status=2");
		return Db::qnfetch($sql, $db)['count'];
	}
	static function get_mutuals($user_one,$user_two,$db){
		$friend_code=2;//status code of friendship
		$sql="
		SELECT `user_id`,concat(`first_name` ,' ',`last_name`) as name FROM `users` WHERE `user_id` IN(
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
	);";
		return Db::fetch_array($db, $sql);
	}
	static function send_friend_req($user_one,$user_two,$db){
		/*
		 * Doing it the wrong way by finding if their row exist and isn't blocked etc and then sending request if possible
		 * Will update in one query in future If I can.
		 * 
		 * Todo:check if both user exist's in first place, a security feature left because no one cares.
		 */
		$sql=Db::create_sql('count(*)count', array('friends'),
				"((`user_one`='$user_two' AND `user_two`='$user_one') OR 
				(`user_one`='$user_one' AND `user_two`='$user_two')) AND status>0"
				);
		if (Db::qnfetch($sql, $db)['count']>0){
			return false;//Friendship can not be sent because either both are already friends or user is blocked.
		}
		$sql=Db::create_sql_insert('friends', array(
				"user_one"=>$user_one,
				"user_two"=>$user_two,
				"status"=>1,
		),null,$db);
		return $db->query($sql)?$db->last_insert_id():false;
	}
}