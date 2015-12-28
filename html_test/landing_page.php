
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="_css/google.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="_js/google.js"></script>
<style>
body {
    background-image: url("title.png");
    background-repeat: no-repeat;
	opacity: 1;
	background-color: ;
    }
.container {
    position: relative;
    width: 100%;
	float: center;
	     position: fixed;
    top: 100px;
    right: 5px;
    
}	.img{
opacity: 1;
    filter: alpha(opacity=40);
}
</style>
  </head>
  <div class="container">
        <div class="card card-container">
		<div id="back_arrow"></div>
		    <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
            <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />
            <p id="profile-name" class="profile-name-card"><?=(isset($_POST['email']))?$_POST['email']:""; ?></p>
            <form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>#password" method="post">
                <span id="reauth-email" class="reauth-email"></span>
				<?php if(!isset($_POST['email'])){   ?>
                <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email address" required autofocus> 
				<?php }
				else {
				?>
				<input type="password" id="inputEmail" class="form-control" name="password" placeholder="password" required autofocus> <?php } ?>
                <?php if(!isset($_POST['email'])){   ?>
				<i class="material-icons"><button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Next</button></i>
				<?PHP }
					else {
				?>
				<i class="material-icons"><button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">SignIn</button></i>
				
				<?PHP } ?>
            </form><!-- /form -->
            <a href="?action=forget_password" class="forgot-password">
                Forgot the password?
            </a>
        </div><!-- /card-container -->
    </div><!-- /container -->
</body>
</html>
<script>  
   function image() {
    var img = document.createElement("IMG");
    img.src = "https://www.gstatic.com/images/icons/material/system/1x/arrow_back_grey600_24dp.png";
	//img.class="back-arrow shift-form";
	//img.aria-label="Back";
	//img.tabindex="0";
	//img.alt="Back"; 
    $('#back_arrow').html(img); 
}     // Append new elements
$("form").submit(function(){
    image();
}); 
</script>
</head>
</html>