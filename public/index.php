<?php

require_once '_includes/include_all.php';
echo clearNoteHtmlOp::get_head();
echo clearNoteHtmlOp::get_include_css();
if (isset($_GET['logout'])){
	Auth::logout();
}
if (isset($_SESSION['user_id'])){
	echo "loggedin";
}else {
	Auth::do_login();
	include TEMPLATE.'loginhome.html';
}
echo clearNoteHtmlOp::get_js();
?>
</body>
</html>