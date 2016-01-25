<?php
require_once '../_includes/config.php';
require_once '../_includes/basic.php';
require_once '../_includes/db.php';
require_once '../_includes/images.php';


$db=new Db();
if (isset($_GET['w'])||isset($_GET['h'])){
	$width=isset($_GET['w'])?$_GET['w']:0;
	$height=isset($_GET['h'])?$_GET['h']:0;
	$size=array($width,$height);
}else {
	$size=isset($_GET['s'])?$_GET['s']:null;
}

if (isset($_GET['user'])){
	Image::get_dp($_GET['user'], $size, $db);
}elseif (isset($_GET['id'])){
	Image::get_image($_GET['id'], $size, $db);
}


?>