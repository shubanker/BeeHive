<?php
/**
 * For creating and Handeling Posts,likes,comments etc
 * 
 * @access
 * 1-public
 * 2-friends only
 * 3-only Me
 */
class Post extends Struct{
	
	function __construct($post_id=null,$db=null){
		$this->table="Post";
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
		return $this->set('access', $access);
	}
	function get_status(){
		return $this->get('status');
	}
	function set_status($status){
		return $this->set('status', $status);
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
		$db->query($sql);
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
	
}