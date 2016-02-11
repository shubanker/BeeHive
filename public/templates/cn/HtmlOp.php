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
	static function get_nav($user_id){
		$op=<<<EOT
<nav class="navbar navbar-default navbar-fixed-top navbar-principal">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                <a class="navbar-brand" href="index.php"><img src="img/logo.png" class="img-logo"> <b>Bee-Hive</b> </a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <div class="col-md-5 col-sm-4">
                    <form class="navbar-form">
                        <div class="form-group" style="display:inline;">
                            <div class="input-group" style="display:table;">
                                <input class="form-control" name="search" placeholder="Search..." autocomplete="off" type="text"> <span class="input-group-addon" style="width:1%;"><span class="glyphicon glyphicon-search"></span></span>
                            </div>
                        </div>
                    </form>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="profile.php"><img src="image.php?user=$user_id&s=m" class="img-nav"></a>
                    </li>
                    <li class="active"><a href="index.php"><i class="fa fa-bars"></i>&nbsp;Home</a>
                    </li>
                    <li><a href="messages.php" id="messages"><i class="fa fa-comments"></i></a>
                    </li>
                    <li><a href="notifications.php" id="notifications"><i class="fa fa-bell-o"></i></a>
                    </li>
                    <li><a href="#" class="nav-controller hide-chat"><i class="fa fa-user"></i>Users</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
EOT;
		return $op;
	}
	static function get_footer($footer_class="welcome-footer"){
		$op=<<<EOT
        <footer class="$footer_class">
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
	static function get_modal_edit(){
		$op=<<<EOS
    <div id="editPost" class="modal fade" role="dialog" aria-labelledby="editPostModelLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit Post</h4>
                </div>
                <div class="modal-body">
                    <textarea name="" class="form-control" id="editPostTextarea"></textarea>
                    <input type="hidden" id="editPostId">
                    <input type="hidden" id="editType">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancle</button>
                    <button type="button" class="btn btn-primary" id="editPostSubmit">Save changes</button>
                </div>
            </div>
        </div>
    </div>
EOS;
		return $op;
	}
	static function get_modal_image(){
		$op=<<<EOS
	<div id="modal-show" class="modal modal-message modal-primary fade" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><i class="fa fa-image"></i>
                </div>
                <div class="modal-body text-center">
                    <div class="img-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
EOS;
		return $op;
	}
	static function get_chat_sidebar(){
		$op=<<<EOS
    <div class="chat-sidebar focus">
        <div class="list-group text-left">
            <p class="text-center visible-xs"><a href="#" class="hide-chat">Hide</a>
            </p>
            <p class="text-center chat-title"><i class="fa fa-weixin"> Chat</i>
             </p>
             <div class="chat_list">
             </div>
            
        </div>
    </div>
EOS;
		return $op;
	}
	static function get_chat_box(){
		$op=<<<EOS
		<div class="chat-window col-xs-10 col-md-3 col-sm-8 col-md-offset-5">
            <div class="col-xs-12 col-md-12 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading top-bar">
                        <div class="col-md-8 col-xs-8">
                            <h3 class="panel-title"><span class="glyphicon glyphicon-comment"></span> <span id="chat_name">Friend</span></h3>
                        </div>
                        <div class="col-md-4 col-xs-4" style="text-align: right;"><a href="#"><span id="minim_chat_window" class="glyphicon glyphicon-minus icon_minim"></span></a><a href="#"><span class="glyphicon glyphicon-remove icon_close"></span></a>
                        </div>
                    </div>
                    <div class="panel-body msg_container_base">
                    <!-- Chat messages -->
                    </div>
                    <div class="panel-footer">
                        <div class="input-group">
                            <input id="btn-input" type="text" class="form-control input-sm chat_input" placeholder="Write your message here..." /> <span class="input-group-btn"><button class="btn btn-primary btn-sm" id="btn-chat">Send</button> </span>
                            <input type="hidden" id='current_chat_user_id' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
EOS;
		return $op;
	}
}