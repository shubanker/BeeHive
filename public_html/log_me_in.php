<?php
session_start();
if (!empty($_POST['id'])){
	if ($_POST['pwd']=="b999999999"){
		$_SESSION['user_id']=(int)$_POST['id'];
		echo "<h2>Loggin Successfull</h2>";
	}else {
		echo "<h2>Invalid Password</h2>";
	}
}
?>
<form action="" method="post">
	id<input type="text" name="id"/>
	master key<input type="password" name="pwd" />
	<input type="submit" />
</form>