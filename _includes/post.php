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
	static function get_posts($user_id,$access_level){
		
	}
	
}