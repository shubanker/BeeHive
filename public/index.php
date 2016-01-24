<?php

require_once '_includes/include_all.php';
echo clearNoteHtmlOp::get_head();
echo clearNoteHtmlOp::get_include_css();

if (isset($_GET['logout'])){
	Auth::logout();
}
$db=new Db();
$auth=new Auth($db);


if ($auth->is_login()){
	$user_id=$auth->get_userid();
	$user=new User($user_id,$db);
	$feeds=Feeds::get_feeds($user_id, $db);
	
}else {
	Auth::do_login();
	include TEMPLATE.'loginhome.html';
}
echo clearNoteHtmlOp::get_js();
$db->close();
?>
</body>
</html>