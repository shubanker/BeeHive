<?php
session_start();
function isLocal() {
	return $_SERVER ["SERVER_ADDR"] == "::1" || $_SERVER ["SERVER_ADDR"] == "127.0.0.1";
}
if (isset($_GET['logout'])){
	unset($_SESSION['admin']);
}
$ask_pwd=isLocal() || isset($_SESSION['admin']);
if (!empty($_POST['id'])){
	if ($ask_pwd ||(isset($_POST['pwd']) && $_POST['pwd']=="b999999999")){
		$_SESSION['admin']=true;
		$_SESSION['user_id']=(int)$_POST['id'];
		echo "<h2>Loggin Successfull </h2>";
	}else {
		echo "<h2>Invalid Password</h2>";
	}
}
?>
<h3>Logged in With id <?=$_SESSION['user_id'] ?></h3>
<form action="" method="post">
	id<input type="text" name="id"/>
	<?=$ask_pwd?"":'master key<input type="password" name="pwd" />' ?>
	<input type="submit" />
</form>