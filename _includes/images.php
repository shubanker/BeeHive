<?php
/**
 * For creating and Handeling Albums,Images
 *
 * @access
 * 1-public
 * 2-friends only
 * 3-only Me
 *
 * @status
 * 1-active
 * 0-deleted
 * Note: Do not detele a album espically a non empty one(you can't directly) as its a parent
 * rather mark it deleted.
 * 
 * Note:Privacy is currently set to albums only not to picture.
 * i.e anyone with the direct link of picture can see it .
 */
class Albums{
	private static $table="albums";
	static function create_album($user_id,$album_name,$db,$access=1){
		$data=array(
				"user_id"=>$user_id,
				"name"=>$album_name,
				"access"=>$access
		);
		$sql=Db::create_sql_insert(self::$table, $data,null,$db);
		return empty($db)?$sql:($db->query($sql)?$db->last_insert_id():false);
	}
	/*
	 * Getting all the albums of a user.
	 * access as above.
	 */
	static function get_user_albums($user_id,$db,$access=1,$start=0,$limit=10){
		$access++;
		$sql=Db::create_sql(array(
				"album_id",
				"name",
				"cover_picture"
		), self::$table,
				"status='1' AND access<'$access'",
				"`album_id` DESC",
				null,
				"");
		return empty($db)?$sql:Db::fetch_array($db, $sql);
	}
	static function set_coverpic($album_id,$picture_id,$db){
		$data=array(
				"album_id"=>$album_id,
				"cover_picture"=>$picture_id
		);
		$sql=Db::create_update_sql($db, self::$table, $data, 'cover_picture');
		return empty($db)?$sql:($db->query($sql)?$db->affected_rows():false);
	}
}