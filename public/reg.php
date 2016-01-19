<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>Fullscreen Form Interface</title>
		<meta name="description" content="Registration Page for Beehive." />
		<meta name="keywords" content="Beehive registration" />
		<meta name="author" content="Shubanker" />
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="_css/normalize.css" />
		<link rel="stylesheet" type="text/css" href="_css/reg.css" />
		<link rel="stylesheet" type="text/css" href="_css/reg-component.css" />
		<link rel="stylesheet" type="text/css" href="_css/reg-cs-select.css" />
		<link rel="stylesheet" type="text/css" href="_css/reg-cs-skin-boxes.css" />
		<script src="_js/reg-modernizr.custom.js"></script>
	</head>
	<body>
		<div class="container">

			<div class="fs-form-wrap" id="fs-form-wrap">
				<form id="myform" class="fs-form fs-form-full" autocomplete="off" action="" method="post">
					<ol class="fs-fields">
						<li>
							<label class="fs-field-label fs-anim-upper" for="q1">What's your name?</label>
							<input class="fs-anim-lower" id="q1" name="name" type="text" placeholder="Sherlock Holmes" required autofocus value="<?=isset($_POST['name'])?$_POST['name']:"" ?>" />
						</li>
						<li>
							<label class="fs-field-label fs-anim-upper" for="q2" data-info="We won't send you spam, we promise...">What's your email address?</label>
							<input class="fs-anim-lower" id="q2" name="email" type="email" placeholder="emaiple@example.com" required value="<?=isset($_POST['email'])?$_POST['email']:"" ?>" />
						</li>
						<li>
							<label class="fs-field-label fs-anim-upper" for="q2" data-info="Set A strong password...">Set a Password for your account!</label>
							<input class="fs-anim-lower" id="q2" name="password" type="password" placeholder="password" required value="" />
						</li>
						
						<li data-input-trigger>
							<label class="fs-field-label fs-anim-upper" for="q3">Tell us What your Gender is?</label>
							<div class="fs-radio-group fs-radio-custom clearfix fs-anim-lower">
								<span><input id="q3b" name="gender" type="radio" value="M"/><label for="q3b" class="radio-mr">Boy</label></span>
								<span><input id="q3c" name="gender" type="radio" value="F"/><label for="q3c" class="radio-miss">Girl</label></span>
							</div>
						</li>
						<li>
							<label class="fs-field-label fs-anim-upper" for="q5" data-info="This will help us serve you approperiate content" >Tell us When you were Born?</label>
							<input class="fs-anim-lower" id="q5" name="dob" type="date" placeholder="YYYY-MM-DD" value="<?=isset($_POST['date'])?$_POST['date']:"" ?>"/>
						</li>
					</ol><!-- /fs-fields -->
					<button class="fs-submit" type="submit">Create Account</button>
				</form><!-- /fs-form -->
			</div><!-- /fs-form-wrap -->

		</div><!-- /container -->
		<script src="_js/classie.js"></script>
		<script src="_js/selectFx.js"></script>
		<script src="_js/fullscreenForm.js"></script>
		<script>
			(function() {
				var formWrap = document.getElementById( 'fs-form-wrap' );

				[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {	
					new SelectFx( el, {
						stickyPlaceholder: false,
						onChange: function(val){
							document.querySelector('span.cs-placeholder').style.backgroundColor = val;
						}
					});
				} );

				new FForm( formWrap, {
					onReview : function() {
						classie.add( document.body, 'overview' );
					}
				} );
			})();
		</script>
	</body>
</html>