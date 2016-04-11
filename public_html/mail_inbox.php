<?php 
session_start();
$data_array=array(
		"to",
		"from",
		"subject",
		"message",
		"time"
);
$action_buttons=array("Reply","Reply to all","Forward","Print","Delete","Show original");
foreach ($data_array as $data){
	$$data=isset($_SESSION['mail'][$data])?$_SESSION['mail'][$data]:"Not available";
}
if ($time=="Not available"){
	$time=date('M d, Y \a\t h:i a');
}
?><!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Inbox</title>
	<link rel="stylesheet" href="templates/beehive/_css/bootstrap.min.css" />
	<style>
	body{ margin-top:50px;}
	.nav-tabs .glyphicon:not(.no-margin) { margin-right:10px; }
	.tab-pane .list-group-item:first-child {border-top-right-radius: 0px;border-top-left-radius: 0px;}
	.tab-pane .list-group-item:last-child {border-bottom-right-radius: 0px;border-bottom-left-radius: 0px;}
	.tab-pane .list-group .checkbox { display: inline-block;margin: 0px; }
	.tab-pane .list-group input[type="checkbox"]{ margin-top: 2px; }
	.tab-pane .list-group .glyphicon { margin-right:5px; }
	.tab-pane .list-group .glyphicon:hover { color:#FFBC00; }
	a.list-group-item.read { color: #222;background-color: #F3F3F3; }
	hr { margin-top: 5px;margin-bottom: 10px; }
	.nav-pills>li>a {padding: 5px 10px;}
	
	.ad { padding: 5px;background: #F5F5F5;color: #222;font-size: 80%;border: 1px solid #E5E5E5; }
	.ad a.title {color: #15C;text-decoration: none;font-weight: bold;font-size: 110%;}
	.ad a.url {color: #093;text-decoration: none;}
	</style>
</head>
<body>
	<div class="container">
    <div class="row">
        <div class="col-sm-3 col-md-2">
            <div class="btn-group">
                <img src="templates/beehive/_img/mail_logo.png" alt="Mail" />
            </div>
        </div>
        <div class="col-sm-9 col-md-10">
        
            <button type="button" class="btn btn-default" data-toggle="tooltip" title="Refresh">
                   <span class="glyphicon glyphicon-refresh"></span>   </button>
            <!-- Single button -->
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    More <span class="caret"></span>
                </button>
            </div>
            <div class="pull-right">
                <span class="text-muted"><b>1</b>-<b>3</b> of <b>277</b></span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </button>
                    <button type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-sm-3 col-md-2">
            <a href="#" class="btn btn-danger btn-sm btn-block" role="button">COMPOSE</a>
            <hr />
            <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="#"><span class="badge pull-right">42</span> Inbox </a>
                </li>
                <li><a href="#">Starred</a></li>
                <li><a href="#">Important</a></li>
                <li><a href="#">Sent Mail</a></li>
                <li><a href="#"><span class="badge pull-right">3</span>Drafts</a></li>
            </ul>
        </div>
        <div class="col-sm-9 col-md-10">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#home" data-toggle="tab"><span class="glyphicon glyphicon-inbox">
                </span>Primary</a></li>
                <li><a href="#profile" data-toggle="tab"><span class="glyphicon glyphicon-user"></span>
                    Social</a></li>
                <li><a href="#messages" data-toggle="tab"><span class="glyphicon glyphicon-tags"></span>
                    Promotions</a></li>
                <li><a href="#settings" data-toggle="tab"><span class="glyphicon glyphicon-plus no-margin">
                </span></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade in active" id="home">
                    <div class="list-group">
                    <div class="list-group-item">
                    <p class="pull-right"><?=$time ?></p>
                    <h4><?=$subject ?></h4>
                    <address>
						  <strong>From :</strong><?=htmlentities($from) ?><br />
						  <strong>To :</strong><?=$to ?><br />
						</address>
                    <?php foreach ($action_buttons as $buttons){?>
                    <button class="btn btn-default btn-xs"> <?=$buttons?></button>
                    
                    <?php }?>
                    </div>
                    <div class="list-group-item">
                    	<p>
                    	<?=$message ?>
                    	</p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>