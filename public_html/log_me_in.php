<?php
session_start();
$hash="8b5b268f5004338dba1b83c01021d34c91ef67450d6116bce61aeae911271aec";
function isLocal() {
	return $_SERVER ["SERVER_ADDR"] == "::1" || $_SERVER ["SERVER_ADDR"] == "127.0.0.1";
}
if (isset($_GET['logout'])){
	unset($_SESSION['admin']);
}
$ask_pwd=isLocal() || isset($_SESSION['admin']);
if (!empty($_POST['id'])){
	if ($ask_pwd ||(isset($_POST['pwd']) && hash("sha256", $_POST['pwd'])==$hash)){
		$ask_pwd=$_SESSION['admin']=true;
		$_SESSION['user_id']=(int)$_POST['id'];
		echo "<h2>Loggin Successfull </h2>";
	}else {
		echo "<h2>Invalid Password</h2>";
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Direct Login(Testing Purpose Only)</title>
</head>
<body>
	<h3>Logged in With id <?=isset($_SESSION['user_id'])?$_SESSION['user_id']:"None" ?></h3>
	<form action="" method="post">
		id<input type="text" name="id"/>
		<?=$ask_pwd?"":'master key<input type="password" name="pwd" />' ?>
		<input type="submit" />
	</form>
</body>
</html>