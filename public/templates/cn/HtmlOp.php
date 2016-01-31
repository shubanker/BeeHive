<?php
class clearNoteHtmlOp{
	static function get_head($current_page="Home",$title="Bee-Hive"){
		$op=<<<EOS
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>$current_page | $title</title>
EOS;
		return $op;
	}
	static function get_include_css($include_css=array()){
		$default_css=array(
				TEMPLATE."_css/bootstrap.min.css",
				TEMPLATE."_css/animate.min.css",
				TEMPLATE."_css/font-awesome.min.css",
				TEMPLATE."_css/timeline.css"
		);
		foreach ($include_css as $css){
			$default_css[]=$css;
		}
		$op="";
		foreach ($default_css as $css){
			$op.=<<<EOS
    <link href="$css" rel="stylesheet">
	
EOS;
		}
		$op.=<<<EOS
</head><!--/head-->

EOS;
		return $op;
	}
	static function get_message_variable(){
	
		if (!empty($_SESSION['msg'])){
			$msg=$_SESSION['msg'];
			unset($_SESSION['msg']);
			return $msg;
		}
		return array();
	}
	static function get_msg($msg,$type="info"){
		if (!is_array($msg)){
			if (!empty($msg)){
				$msg=array($type=>array($msg));
			}
		}
		$op="";
		foreach ($msg as $type=>$messages){
			foreach ($messages as $message){
				$op=<<<EOS
<div class="alert alert-$type">
	<button type="button" class="close" data-dismiss="alert"> &times;</button>
	$message
</div>
EOS;
			}
		}
		return $op;
	
	}
	static function get_footer(){
		$op=<<<EOT
        <footer class="welcome-footer">
            <div class="container">
                <p>
                    <div class="footer-links"><a href="#">Terms of Use</a> | <a href="#">Privacy Policy</a> | <a href="#">Developers</a> | <a href="#">Contact</a> | <a href="#">About</a>
                    </div>Copyright &copy; Company - All rights reserved</p>
            </div>
        </footer>
EOT;
		return $op;
	}
	static function get_js($includes=array()){
		$default=array(
				TEMPLATE."_js/jquery.js",
				TEMPLATE."_js/bootstrap.min.js",
				TEMPLATE."_js/custom.js"
		);
		foreach ($includes as $value) {
			$default[]=TEMPLATE."_js/$value";
		}
		$op="";
		foreach ($default as $js){
			$op.=<<<EOS
    <script src="$js"></script>
	
EOS;
		}
	
		return $op;
	}
}