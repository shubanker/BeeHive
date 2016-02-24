<?php
/**
 * For creating and Handeling Posts,likes,comments etc
 * 
 * @access
 * 1-public
 * 2-friends only
 * 3-only Me
 * 
 * @status
 * 1-active
 * 0-deleted
 * Note: Do not detele a post(you can't directly) as its a parent 
 * rather mark it deleted.
 */
class Post extends Struct{
	
	function __construct($post_id=null,$db=null){
		$this->table="post";
		$this->pk="post_id";
		$this->data=array();
		
		$this->fields=array(
				"post_id",
				"user_id",
				"post_data",
				"link",
				"picture_id",
				"time",
				"access",
				"status"
		);
		if (!$this->initialise_by_id($post_id, $db)){
			//Setting some default values.
			$this->set_status(1);
			$this->set_access(1);
		}
	}
	function get_post_id(){
		return $this->get('post_id');
	}
	function get_user_id(){
		return $this->get('user_id');
	}
	function set_user_id($user_id){
		return $this->set('user_id', $user_id);
	}
	function get_post_data(){
		return $this->get('post_data');
	}
	function set_post_data($post_data){
		return $this->set('post_data', $post_data);
	}
	static function update_post_data($user_id,$post_id,$data,$db){
		$sql=Db::create_update_sql2('post', array(
				'post_data'=>$data
		), array(
				'post_id'=>(int)$post_id
		), $db);
		return empty($db)?$sql:$db->query($sql);
	}
	function get_link(){
		return $this->get('link');
	}
	function set_link($link){
		return $this->set('link', $link);
	}
	function get_picture_id(){
		return $this->get('picture_id');
	}
	function set_picture_id($picture_id){
		return $this->set('picture_id', $picture_id);
	}
	function get_time(){
		return $this->get('time');
	}
	function set_time($time){ //Time should not be updated of a post thou.
		return $this->set('time', date("Y-m-d H:i:s",strtotime($time)));
	}
	function get_access(){
		return $this->get('access');
	}
	function set_access($access){
		if (!is_numeric($access)||$access<1||$access>3){
			$access=2;//2 is default i.e friends only.
		}
		return $this->set('access', (int)$access);
	}
	function get_status(){
		return $this->get('status');
	}
	function set_status($status){
		return $this->set('status', $status);
	}
	static function set_post_status($user_id,$post_id,$status,$db){
		$sql=Db::create_update_sql2('Post', array(
				'status'=>(int)($status)
		), array(
				'post_id'=>$post_id,
				'user_id'=>$user_id
		), $db);
		return empty($db)?$sql:$db->query($sql);
	}
	/*
	 * @type
	 * 1-post
	 * 2-comment
	 */
	private static function like($user_id,$post_id,$type,$db){
		$sql=Db::create_sql_insert('likes', array(
				"user_id"=>$user_id,
				"post_id"=>$post_id,
				"type"=>$type
		),null,$db);
		return $db->query($sql);
	}
	static function like_post($user_id,$post_id,$db){
		return self::like($user_id, $post_id, 1, $db);
	}
	static function like_comment($user_id,$comment_id,$db){
		return self::like($user_id, $comment_id, 2, $db);
	}
	private static function unlike($user_id,$post_id,$type,$db){
		$sql=Db::create_sql_delete('likes', array(
				"user_id"=>$user_id,
				"post_id"=>$post_id,
				"type"=>$type));
		return $db->query($sql);
	}
	static function unlike_post($user_id,$post_id,$db){
		return self::unlike($user_id, $post_id, 1, $db);
	}
	static function unlike_comment($user_id,$comment_id,$db){
		return self::unlike($user_id, $comment_id, 2, $db);
	}
	private static  function count_likes($post_id,$type,$db){
		$sql=Db::create_sql('count(*)likes', 'likes',array(
				"post_id"=>$post_id,
				"type"=>$type
		));
		if (empty($db)){
			return $sql;
		}
		$result=$db->qfetch($sql);
		return $result['likes'];
	}
	static function count_post_likes($post_id,$db){
		return self::count_likes($post_id, 1, $db);
	}
	static function count_comments_likes($comment_id,$db){
		return self::count_likes($comment_id, 2, $db);
	}
	private static function get_likers($post_id,$type,$db){
		$sql=Db::create_sql(array(
				'first_name',
				"last_name",
				"users.user_id"
		), array(
				"users",
				"likes"
		),"
				`post_id`='$post_id' AND 
				`likes`.`type`='$type' AND
				`users`.`user_id`=`likes`.`user_id`"
		);
		return Db::fetch_array($db, $sql);
	}
	static function get_post_likers($post_id,$db){
		return self::get_likers($post_id, 1, $db);
	}
	static function get_comments_likers($comment_id,$db){
		return self::get_likers($comment_id, 2, $db);
	}
	
	static function add_comment($user_id,$post_id,$comment,$db){
		$sql=Db::create_sql_insert('comments', array(
				"user_id"=>$user_id,
				"post_id"=>$post_id,
				"comment"=>$comment,
				"status"=>1
		),null,$db);
		return $db->query($sql)?$db->last_insert_id():false;
	}
	static function remove_comment($comment_id,$db){
		$sql=Db::create_sql_delete('comments', array(
				"comment_id"=>$comment_id
		));
		return $db->query($sql)?$db->affected_rows():false;
	}
	static function get_comment($comment_id,$db){
		$scomment_id=$spost_id=$db->escape($comment_id);
		$sql=Db::create_sql('*', 'comments',"`comment_id`='$scomment_id'");
	}
	static function edit_comment($comment_id,$new_comment,$db,$user_id=null,$admin=FALSE){
		$where=array(
				"comment_id"=>$comment_id
		);
		if(!$admin){
			$where['user_id']=(int)$user_id;
		}
		$sql=Db::create_update_sql2('comments', array(
				"comment"=>$new_comment
		), $where, $db);
		return $db->query($sql)?$db->affected_rows():false;
	}
	static function get_post_comments($user_id,$post_id,$db){
		$spost_id=(int)$post_id;
		$suser_id=(int)$user_id;
		$sql=Db::create_sql("
				`first_name`,
				`last_name`,
				`users`.`user_id`,
				`comment_id`,
				`comment`,
				`time`,
				(SELECT  count(*) FROM likes WHERE `likes`.`post_id` =`comments`.`comment_id` AND `type` ='2')as like_count,
				(SELECT count(*) FROM likes WHERE `likes`.`post_id` =`comments`.`comment_id` AND `type` ='2' AND `likes`.`user_id`='$suser_id') as has_liked
				",
				 array(
				"users",
				"comments"
		),
				"`post_id`='$spost_id' AND
				`users`.`user_id`=`comments`.`user_id` AND
				`comments`.`status`=1",
				"`comment_id` DESC");
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
}
class Feeds{
	/*
	 * Function returnds the posts of friends to be displayed in homepage.
	 */
	static function get_feeds($user_id,$db,$start=NULL,$limit=NULL,$after_post_id=NULL,$equality=">",$specific_post_id=NULL){
		// 		$db=new Db($user, $password, $database);
		$start=empty($start)?0:$start;
		$limit=empty($limit)?10:$limit;
		$equality=$equality=="<"?"<":">";
		$after_post_id=empty($after_post_id)?"":"`post`.`post_id` $equality '$after_post_id' AND ";
		
		if (!empty($specific_post_id)){
			$after_post_id="`post`.`post_id` = '$specific_post_id' AND ";
			$start=0;
			$limit=1;
		}
		
		$friend_list=Friendship::get_friend_ids($user_id, null);
		$following_list=Friendship::get_following_ids($user_id, null);
		$sql=Db::create_sql(
				"`first_name`,
				`last_name`,
				`post_id`,
				`users`.`user_id`,
				`post_data`,
				`link`,
				`picture_id`,
				`time`,
				(SELECT  count(*) FROM likes WHERE `likes`.`post_id` =`post`.`post_id` AND `type` ='1')as like_count,
				(SELECT  count(*) FROM comments WHERE `comments`.`post_id` =`post`.`post_id` AND `status` ='1')as comment_count,
				(SELECT count(*) FROM likes WHERE `likes`.`post_id` =`post`.`post_id` AND `type` ='1' AND `likes`.`user_id`='$user_id') as has_liked,
				`access`",

				array(
				"users",
				"post"
		),
				"(
					(
						`post`.`user_id` IN($friend_list) AND  				-- For including friends post
						`post`.`access` < 3
					) OR
					(
						`post`.`user_id` IN($following_list) AND  				-- For including friends post
						`post`.`access` < 2
					) OR
						`post`.`user_id`='$user_id'  				-- For including own post's
				) AND
				$after_post_id
				`post`.`status`='1' AND
				`post`.`user_id`=`users`.`user_id`",
				"`post_id` DESC",
				null,
				"$start,$limit"
		);
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	
	}
	static function get_friends_feeds($user_one,$user_two,$db,$start=NULL,$limit=NULL,$after_post_id=NULL,$equality=">"){
		
		$start=empty($start)?0:$start;
		$limit=empty($limit)?10:$limit;
		$equality=$equality=="<"?"<":">";
		$after_post_id=empty($after_post_id)?"":"`post`.`post_id` $equality '$after_post_id' AND ";
		
		if ($user_one==$user_two){
			$access_limit=4; //Checking if the user is viewing his own profile.
		}else {
			$relation=Friendship::get_relation($user_one, $user_two, $db);
			if ($relation==3){
				return null;//If user is blocked.
			}
			$access_limit=$relation==2?3:2;
		}
		
		$sql=Db::create_sql("`first_name`,
				`last_name`,
				`post_id`,
				`users`.`user_id`,
				`post_data`,
				`link`,
				`picture_id`,
				`time`,
				(SELECT  count(*) FROM likes WHERE `likes`.`post_id` =`post`.`post_id` AND `type` ='1')as like_count,
				(SELECT  count(*) FROM comments WHERE `comments`.`post_id` =`post`.`post_id` AND `status` ='1')as comment_count,
				(SELECT count(*) FROM likes WHERE `likes`.`post_id` =`post`.`post_id` AND `type` ='1' AND `likes`.`user_id`='$user_one') as has_liked,
				`access`"
				, array(
				"users",
				"post"
		),
				"`post`.`user_id`='$user_two' AND
				$after_post_id
				`post`.`status`='1' AND
				`post`.`access` < '$access_limit' AND
				`post`.`user_id`=`users`.`user_id`",
				"`post_id` DESC",
				null,
				"$start,$limit"
		);
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
	static function get_age($time){
		
		$diff=strtotime("now")-strtotime($time);
		
		$hour=round($diff/3600);
		$min=round(($diff%3600)/60);
		$sec=$diff%60;
		$age="";
	    if($hour > 24) $days = round($hour / 24);
	    else $days = 0;
	
	    if($days >= 31) {
	        $date = date('M d, Y', strtotime("-$hour hours"));
	        return $date;
	    }
	    else if($days >= 1) {
	        $age = "$days day";
	        if($days > 1) { $age .= "s"; }
	    }
	    else {
	        if($hour > 0) {
	            $hour = ltrim($hour, '0');
	            $age .= " $hour hour";
	            if($hour > 1) { $age .= "s"; }
	        }
	        if($min > 0) {
	            $min = ltrim($min, '0'); 
	            if(!$min) $min = '0';
	            $age .= " $min min";
	            if($min != 1) { $age .= "s"; }
	        }
	    }
	
	    if($min < 1 and $hour < 1) { $age = 'a few seconds'; }
	    $age .= ' ago';
	
	    return $age;
	}
	static function get_friends_images($user_one,$user_two,$db,$start=NULL,$limit=NULL,$after_post_id=NULL,$equality=">"){
		
		$start=empty($start)?0:$start;
		$limit=empty($limit)?10:$limit;
		$equality=$equality=="<"?"<":">";
		$after_post_id=empty($after_post_id)?"":"`post`.`post_id` $equality '$after_post_id' AND ";
		
		if ($user_one==$user_two){
			$access_limit=4; //Checking if the user is viewing his own profile.
		}else {
			$relation=Friendship::get_relation($user_one, $user_two, $db);
			if ($relation==3){
				return null;//If user is blocked.
			}
			$access_limit=$relation==2?3:2;
		}
		$sql=Db::create_sql(array('picture_id'), 'post',
				"`post`.`user_id`='$user_two' AND
				`picture_id` IS NOT NULL AND
				`post`.`status`='1' AND
				`post`.`access` < '$access_limit'",
				"`post_id` DESC",
				null,
				"$start,$limit"
			);
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
}