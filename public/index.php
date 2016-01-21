<?php

require_once '_includes/include_all.php';
echo clearNoteHtmlOp::get_head();
echo clearNoteHtmlOp::get_include_css();
if (isset($_SESSION['user_id'])){
	echo "loggedin";
}else {
	if (isset($_POST['email'])){
		if (isset($_POST['password'])){
			$db=new Db(DBUSER, DBPASSWORD, DATABASE);
			$auth=new Auth($db);
			if ($auth->check_crediantials($_POST['email'], $_POST['password'], $db)){
				redirect_to();
			}
		}
	}
	include TEMPLATE.'loginhome.html';
}
echo clearNoteHtmlOp::get_js();
?>
</body>
</html>