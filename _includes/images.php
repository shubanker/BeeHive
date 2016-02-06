<?php
/*
 * No privacy has been added or required in displaying Image
 * if the user has direct link or the image id of the image he can view it.
 */
class Image{
	
	static function new_image($image_name,$db){
		$up=new upload(IMAGEDIR."/");
		
		if ($up->upload_image_file($image_name)){
			
			$file_name=$up->get_file_name();
			$sql=Db::create_sql_insert("images", array(
					"loc"=>$file_name
			));
			if ($db->query($sql)){
				return $db->last_insert_id();
			}
			
		}
		return false;
		
	}
	static function get_dp($user_id,$size,$db){
		$sql=Db::create_sql("IFNULL(`data`,1)",
				 array("userdata"),
				"`user_id`='$user_id' AND
				`type`='dp' AND
				`status`=1",
				null,
				null,
				1
			);
		
		self::get_image($sql, $size, $db,true);
	}
	static function get_image($image_id,$size,$db,$isdp=false,$showdeleted=FALSE){
		$qimage=$isdp?"($image_id)":"'$image_id'";
		
		$status_condition=$showdeleted?"":" AND status='1' ";
		$sql=Db::create_sql(array(
				"loc"
		), array(
				"images"
		),
				"`image_id`=$qimage $status_condition");
		$image=Db::qnfetch($sql, $db);
		self::thumbnail($image['loc'], $size);
	}
	private static function thumbnail($image, $size=NULL) {
		/*
		 if($image[0] != "/") { // Decide where to look for the image if a full path is not given
			if(!isset($_SERVER["HTTP_REFERER"])) { // Try to find image if accessed directly from this script in a browser
			$image = $_SERVER["DOCUMENT_ROOT"].implode("/", (explode('/', $_SERVER["PHP_SELF"], -1)))."/".$image;
			} else {
			$image = implode("/", (explode('/', $_SERVER["HTTP_REFERER"], -1)))."/".$image;
			}
			} else {
			$image = $_SERVER["DOCUMENT_ROOT"].$image;
			}
			*/
		header("Pragma: public");
		header("Cache-Control: max-age = 604800");
		header("Expires: ".gmdate("D, d M Y H:i:s", time() + 604800)." GMT");
		
		if (empty($image)||!file_exists(IMAGEDIR."/$image")){
			header("HTTP/1.0 404 Not Found");
			die();
		}
		$image=IMAGEDIR."/$image";
		
		$image_properties = getimagesize($image);
		$image_width = $image_properties[0];
		$image_height = $image_properties[1];
		$image_ratio = $image_width / $image_height;
		$type = $image_properties["mime"];
		
		switch ($size){
			case "s":
				$max_len=160;
				break;
			case "es":
				$max_len=80;
				break;
			case "m":
				$max_len=640;
				break;
			default:
				if (is_array($size)){
					$width=isset($size[0])?$size[0]:null;
					$height=isset($size[1])?$size[1]:null;
				}
		}
		if (isset($max_len)){
			if ($image_width>$image_height){
				$width=$image_width>$max_len?$max_len:$image_width;
				$height=null;
			}else {
				$height=$image_height>$max_len?$max_len:$image_height;
				$width=null;
			}
		}
	
		if(empty($width) && empty($height)) {
			$width = $image_width;
			$height = $image_height;
		}
		if(empty($width)) {
			$width = round($height * $image_ratio);
		}
		if(empty($height)) {
			$height = round($width / $image_ratio);
		}
	
		if($type == "image/jpeg") {
			header('Content-type: image/jpeg');
			$thumb = imagecreatefromjpeg($image);
		} elseif($type == "image/png") {
			header('Content-type: image/png');
			$thumb = imagecreatefrompng($image);
		} else {
			return false;
		}
	
		$temp_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($temp_image, $thumb, 0, 0, 0, 0, $width, $height, $image_width, $image_height);
		$thumbnail = imagecreatetruecolor($width, $height);
		imagecopyresampled($thumbnail, $temp_image, 0, 0, 0, 0, $width, $height, $width, $height);
	
		if($type == "image/jpeg") {
			imagejpeg($thumbnail);
		} else {
			imagepng($thumbnail);
		}
	
		imagedestroy($temp_image);
		imagedestroy($thumbnail);
	
	}
}