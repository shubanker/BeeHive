<?php

require_once '_includes/include_all.php';
echo clearNoteHtmlOp::get_head();
echo clearNoteHtmlOp::get_include_css();
if (isset($_SESSION['user_id'])){
	echo "loggedin";
}else {
	include 'loginhome.html';
}
echo clearNoteHtmlOp::get_js();
?>
</html>