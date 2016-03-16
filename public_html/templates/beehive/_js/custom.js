$(document).ready(function() {
  $('.alert-info').hide();
  $('.tip').tooltip();
  vanish_placeholder($('input[autofocus],textarea[autofocus]'));
  load_online_list();
  get_notification_count(false);
  
});
/*============ Chat sidebar ========*/
//$('.chat-sidebar, .nav-controller, .chat-sidebar a').on('click', function(event) {
//  $('.chat-sidebar').toggleClass('focus');
//});

$(".hide-chat").click(function(e){
	e.preventDefault();
	$('.chat-sidebar').toggleClass('focus');
});
/*Password Eye */
$(".password").on("keyup",function(){
    var icon=$(this).parent().children("span");
    if($(this).val())
    	icon.show();
    else
    	icon.hide();
    
});
$(".glyphicon-eye-open").mousedown(function(){
		$(".password").attr('type','text');
    }).mouseup(function(){
    	$(".password").attr('type','password');
    }).mouseout(function(){
    	$(".password").attr('type','password');
    });

/*show image in modal when click*/
$(document).on('click','.show-in-modal',function(e){
	if (/.*id=(\d+).*/i.test($(this).attr('src'))) {
		r=r=$(this).attr('src').split(/.*id=(\d+).*/i);
		imgscr="image.php?id="+r[1];
	} else {
		imgscr=$(this).attr('src');
	}
  $('#modal-show .img-content').html('<img class="img-responsive img-rounded" src="'+imgscr+'" />');
    $('#modal-show').modal('show');
    e.preventDefault();
});

/*chat box*/
$(document).on('click', '.chat-sidebar .list-group .list-group-item', function (e) {
  e.preventDefault();
  clearTimeout(chat_timer);
  chat_timer=0;
  can_load_upper_chat=true;
  //Chatbox user name
  name=$(this).find('.chat-user-name').html();
  friendid=$(this).find('input').val();
  
  $('#chat_name').html("<a href='profile.php?id="+friendid+"'>"+name+"</a>");
  $('.chat-window').show();
  $('.chat_input').val('').focus();
  $('#current_chat_user_id').val(friendid);
  load_chat(friendid);
});

$(document).on('click', '.icon_close', function (e) {
	e.preventDefault();
	$(this).closest('.chat-window').hide();
	$('#current_chat_user_id').val('')
	clearTimeout(chat_timer);
	chat_timer=0;
});

$(document).on('click', '.panel-heading span.icon_minim', function (e) {
  var $this = $(this);
  if (!$this.hasClass('panel-collapsed')) {
      $this.parents('.panel').find('.panel-body').slideUp();
      $this.addClass('panel-collapsed');
      $this.removeClass('glyphicon-minus').addClass('glyphicon-plus');
  } else {
      $this.parents('.panel').find('.panel-body').slideDown();
      $this.removeClass('panel-collapsed');
      $this.removeClass('glyphicon-plus').addClass('glyphicon-minus');
  }
});

/*============= About page ==============*/
$(".about-tab-menu .list-group-item").click(function(e) {
  e.preventDefault();
  $(this).siblings('a.active').removeClass("active");
  $(this).addClass("active");
  var index = $(this).index();
  $("div.about-tab>div.about-tab-content").removeClass("active");
  $("div.about-tab>div.about-tab-content").eq(index).addClass("active");
});
/*==============  show panel ===============*/
$(".btn-frm").click(function(){
$(".frm").toggleClass("hidden");
$(".frm").toggleClass("animated");
$(".frm").toggleClass("fadeInRight");
});
/*==============  Statup Post ===============*/
$("#statusbox").on('focus',function(){
  $("#statusboxfooter").removeClass("hidden");
});
/* ============= Online users ================= */
load_online_list_timer=0;
function load_online_list(){
	$.post("ajax-req.php",{req_type:"online_list"}).done(function(d){
		ob=JSON.parse(d);
		$op="";
		if(ob.go_to_friends_page==1){//If user has No friends..
			if($(".post-box-top").length!=0 && (typeof user_id=== 'undefined' || user_id==friend_id)){//If user is  on home or timeline page.
				window.location='friends.php';
			}
		}else{
			for (var i = 0; i < ob.length; i++) {
				$op+=make_chatlist_html(ob[i]);
			}
			if(ob.length>0){
				$(".chat_list").html($op);
			}
			
		}
		
	});
	load_online_list_timer=setTimeout('load_online_list()',15000);
}
function make_chatlist_html(ob){
	$op="<a href='#' class='list-group-item'><i class='fa ";
		$op+=ob.data>200?"fa-circle absent-status":"fa-circle connected-status";
	$op+="'></i>";
	$op+="<input type='hidden' value='"+ob.user_id+"'/>";
	$op+="<img src='image.php?user="+ob.user_id+"&s=s' class='img-chat img-thumbnail'> <span class='chat-user-name'>";
	$op+=(ob.first_name==null?"":ob.first_name)+" "+(ob.last_name==null?"":ob.last_name);
	$op+="</span> </a>";
	return $op;
}
/* ============= Messages ============== */
chat_timer=0;
can_load_upper_chat=true;
msg_data={};
function load_chat(friendid,lastsync){
	if(lastsync === undefined){lastsync=0;}
	cache_msg='';
	/* populating with cached data */
	if(msg_data[friendid] !== undefined ){
		for (i in msg_data[friendid]) {
			cache_msg+=make_chat_msg_html(msg_data[friendid][i],friendid);
		}
	}
	$('.msg_container_base').html(cache_msg);
	$('.msg_container_base').scrollTop($('.msg_container_base')[0].scrollHeight);
	emotify('chat');
	sync_chat();
}
function sync_chat(lastsync,fillbefore){
	if(lastsync === undefined){lastsync=null}
	if(fillbefore === undefined){fillbefore=false}
	if(lastsync==null){
		if(!fillbefore){
			lastsync=$('.messages>input').last().val();
		}else{
			lastsync=$('.messages>input').val();
		}
		lastsync=(lastsync==null?0:lastsync);
	}
	friendid=$('#current_chat_user_id').val();
	if(friendid==null || friendid==''){
		return false;
	}
	$.post("ajax-req.php",{req_type:"get_msg",'lastsync':lastsync,'friendid':friendid,'fillbefore':(fillbefore?0:1)}).done(function(d){
		ob=JSON.parse(d);
		if(ob.length>0){
			lastsync=ob[ob.length-1].message_id;
			op="";
			if(msg_data[friendid] === undefined ){msg_data[friendid]={};}//initialising cache for friend id
			for (var i = 0; i < ob.length; i++) {
				msg_data[friendid][ob[i].message_id]=ob[i];//saving for cache data.
				op+=make_chat_msg_html(ob[i],friendid);
			}
			if(fillbefore){
				$('.msg_container_base').prepend(op);
				can_load_upper_chat=true;
			}else{
				$('.msg_container_base').append(op);
				$('.msg_container_base').scrollTop($('.msg_container_base')[0].scrollHeight);
			}
			emotify('chat');
		}
		chat_timer=setTimeout("sync_chat()",2000);
	});
}
$('.msg_container_base').on('scroll',function(){
	if($(this).scrollTop()<150 && can_load_upper_chat){
		can_load_upper_chat=false;
		friendid=$('#current_chat_user_id').val();
		sync_chat(null,true);
	}
});
$(document).on('click','.btn-sm',function(){
	firendid=$('#current_chat_user_id').val();
	msg=$('.chat_input').val();
	send_msg(msg,friendid);
});
$( document ).on('keyup', '.chat_input' ,function(d){
  if(13==d.keyCode){
	  firendid=$('#current_chat_user_id').val();
	  msg=$('.chat_input').val();
	  send_msg(msg,friendid);
  }
});
function send_msg(msg,friendid,from_messsage){
	if(from_messsage === undefined){from_messsage=false}
	if(msg==null||msg==""){
		return false;
	}
	random_id="temp_"+Math.floor(Math.random()*1000);
	temp_ob={"message_id":random_id,"user_one":user_id,"user_two":friendid,"message":msg,"time":"sending","status":-1};
	
	/*
	 * This functions handles both for message page as wel as chatbox,
	 * and both have different classes,id's,functions to work with.
	 * So we define them and use them in the function.
	*/
	if(from_messsage){
		msg_box='#message_textarea';
		to_append='.chat';
		remove_base='.clearfix';
		remove_up='li';
		html_func=make_msg_html;
		type='message';
	}else{
		msg_box='.chat_input';
		to_append='.msg_container_base';
		remove_base='.messages';
		remove_up='.msg_container';
		html_func=make_chat_msg_html;
		type='chat';
	}
	$(to_append).append(html_func(temp_ob,friendid)).scrollTop($(to_append)[0].scrollHeight);
	emotify(type);
	$(msg_box).val('');
	
	$.post("ajax-req.php",{req_type:"send_msg",'msg':msg,'friendid':friendid}).done(function(d){
		$(remove_base+'>input[value="'+random_id+'"]').parents(remove_up).remove();//removing temporary message.
		
		ob=JSON.parse(d);
		
		if(ob.success==1){
			$(to_append).append(html_func(ob,friendid)).scrollTop($(to_append)[0].scrollHeight);
			emotify(type);
			msg_data[friendid][ob.message_id]=ob;
		}else{
			$(msg_box).val(msg);
		}
		
	}).fail(function(){
		$(remove_base+'>input[value="'+random_id+'"]').parents(remove_up).remove();//removing temporary message.
		$(msg_box).val(msg);
	});
}
function make_chat_msg_html(ob,friendid){
	
	if(friendid!=$('#current_chat_user_id').val()){//Avoiding loading of chats from diff user inCase though.
		return '';
	}
	if($('.messages>input[value="'+ob.message_id+'"]').length>0){//Avoiding repetation of messages..
		return '';
	}
	isreceived=ob.user_one==friendid;
	op="<div class='row msg_container "+(isreceived?"base_receive":"base_sent")+"'>";
	image="<div class='col-md-2 col-xs-2 avatar-chat-box'>" +
			"<img src='image.php?user="+ob.user_one+"&s=s' class=' img-responsive ' alt='image'>" +
			"</div>";
	if(isreceived){
		op+=image;
	}
	op+="<div class='col-md-10 col-xs-10'>" +
			"<div class='messages "+(isreceived?"msg_receive":"msg_sent")+"'>";
	op+="<p>"+ob.message+"</p>" +
			"<time>";
	if(!isreceived){
		op+="<i class='fa  "+(ob.status > 1 ? "fa-check" : (ob.status == -1 ? "fa-clock-o" : "fa-send"))+"'></i> ";
	}
	op+=ob.time+"</time>" +
					"<input type='hidden' value='"+ob.message_id+"'/>" +
					"</div>" +
				"</div>";
	if(!isreceived){
		op+=image;
	}
	op+="</div>";
	return op;
}
/*========== Notification count ========*/
notification_count_timer=0;
function get_notification_count(refresh_notification){
	if(refresh_notification === undefined){refresh_notification=true}
	$.post('ajax-req.php',{req_type:'notification_count'}).done(function(d){
		ob=JSON.parse(d);
		$('.notification_count').remove();
		$('#notifications').append(get_notification_count_html(ob.notification_count));
		$('#messages').append(get_notification_count_html(ob.message_count));
		if(Number(ob.notification_count)>0 && refresh_notification){
			try{//This should work only in notifications page.
//				load_notifications(0,null);
				setTimeout('load_notifications(0,null)',2000);
			}catch(e){
				
			}
		}
	});
	notification_count_timer=setTimeout("get_notification_count()",6000);
}
function get_notification_count_html(c){
	if(c > 0){
		return "<span class='notification_count'>"+c+"</span>";
	}
	return false;
}
$(document).on('click','#friend_action,#block_user',function(e){
	e.preventDefault();
	$this=$(this);
	req_type=$this.attr('id');//Checking if its to block user or other actions
	
	current_action = req_type == "block_user" ? "Block User" : $this.find('span').html();
	
	fid=$this.parents('.card-body-social')[0]?$this.parent().find('button').val():friend_id;
	fname=$this.parents('.card-body-social')[0]?$this.parents('.media-body').find('.friend_list_link').html():$('.user_full_name').html();
	
	/* making Proper Message to display */
	switch(current_action){
		case 'Cancle Request':
			message="Cancle friend request to "+fname;
			confirm_label="Cancle Request";
			cancle_label="Don't Cancle";
			btn_class="btn-warning";
			break;
		case 'Accept Request':
			message="Accept "+fname+" Friend Request";

			confirm_label="Accept";
			cancle_label="Cancle";
			
			btn_class="btn-success";
			break;
		case 'Un Friend':case 'Unfriend':
			message="Remove "+fname+" from your friend";

			confirm_label="Unfriend";
			cancle_label="Not now";
			
			btn_class="btn-warning";
			break;
		case'Un Block':
			message=current_action+" "+fname;

			confirm_label="Unblock";
			cancle_label="Not now";
			
			btn_class="btn-success";
			break;
		case "Block User":
			message="Block "+fname;

			confirm_label="Block";
			cancle_label="Not now";
			
			btn_class="btn-danger";
			break;
		case'Add Friend':
			message="Send Friend Request to "+fname;
			
			confirm_label="Send Request";
			cancle_label="Cancle";
			btn_class="btn-success";
			break;
		default:
			message=current_action+" to "+fname;
			confirm_label="Ok";
			cancle_label="Cancle";
			btn_class="btn-primary";
	}
	bootbox.confirm({
		title:current_action+" ?",
		message:message+" ?",
		buttons:{
			'confirm':{
			      label:confirm_label,
			      className:'btn '+btn_class
			    },
			'cancel':{
			      label:cancle_label,
			      className:'btn-default btn'
			    }
		},
		callback:function(result){
			if(result){
				$.post('ajax-req.php',{req_type:req_type,friend_id:fid}).done(function(d){
					ob=JSON.parse(d);
					if(ob.success==1){
						if(req_type=='block_user'){
							window.location='index.php';
						}else{
							$this.find('span').html(ob.new_action);
						}
						
					}
				});
			}
		}
	});
});
$(document).on('click','#block_userr',function(e){
	e.preventDefault();
	if(confirm("Block this user ?")){
		$this=$(this);
		fid=$this.parents('.card-body-social')[0]?$this.parent().find('button').val():friend_id;
		$.post('ajax-req.php',{req_type:"block_user",friend_id:fid}).done(function(d){
			ob=JSON.parse(d);
			if(ob.success==1){
				window.location='index.php';
			}
		});
	}
});
/* Placeholder Vanisher */
$(document).on('focusin','input,textarea',function(){
	vanish_placeholder($(this));
}).on('focusout','input,textarea',function(){
	appear_placeholder($(this));
});
function vanish_placeholder($this){
	if($this.attr('placeholder')!=""){
		$this.attr('placeholderback',$this.attr('placeholder'));
		$this.attr('placeholder','');
	}
}
function appear_placeholder($this){
	$this.attr('placeholder',$this.attr('placeholderback'));
}

/* Search js*/
$( document ).on('keyup', '#search_box' ,function(d){
	$('#search_form').attr('action',/^inpost:.*/i.test($(this).val())?'index.php':'search.php');
});

/* Emoticons */
function emotify(type){
	 if(type === undefined){type='post'}
	 var options={
			 delay: 0
	 };
	switch(type){
		case 'post':
			$('.post-description').find('p').emoticonize(options);
			$('.post-description').find('p>.hidden_status').emoticonize(options);
			break;
		case 'chat':
			$('.messages>p').emoticonize(options);
			break;
		case 'message':
			$('.chat-body>p').emoticonize(options);
			break;
		case 'comment':
			$('.comment-body>p').emoticonize(options);
			break;
	}
}
/* Message */
function show_msg(title,message,type,callback){
	if(title === undefined){title="";}
	if(message === undefined){message = "Empty.";}
	options={
			title:title,
			message:message+""
		};
	if(type != undefined){
		button={
				label:'Ok',
    			className:'btn-primary btn'
    	};
		switch(type){
		case 'error':case 'danger':
			button.className='btn-danger btn';
			break;
		case 'success':
			button.className='btn-success btn';
			break;
		case 'warning':
			button.className='btn-warning btn';
			break;
		case 'info':
			button.className='btn-info btn';
			break;
		}
		options.buttons={'ok':button};
	}
	if(callback != undefined){
		options.callback=callback;
	}
	bootbox.alert(options);
}