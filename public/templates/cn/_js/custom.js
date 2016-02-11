$(document).ready(function() {
  $('.alert-info').hide();
  $('.tip').tooltip();
  load_online_list();
  get_notification_count();
  
});
/*============ Chat sidebar ========*/
//$('.chat-sidebar, .nav-controller, .chat-sidebar a').on('click', function(event) {
//  $('.chat-sidebar').toggleClass('focus');
//});

$(".hide-chat").click(function(e){
	e.preventDefault();
	$('.chat-sidebar').toggleClass('focus');
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
		for (var i = 0; i < ob.length; i++) {
			$op+=make_chat_html(ob[i]);
		}
		$(".chat_list").html($op);
	});
	load_online_list_timer=setTimeout('load_online_list()',15000);
}
function make_chat_html(ob){
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
function load_chat(friendid,lastsync=0){
	$('.msg_container_base').html('');
	sync_chat(lastsync);
}
function sync_chat(lastsync=null,fillbefore=false){
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
			for (var i = 0; i < ob.length; i++) {
				op+=make_chat_msg__html(ob[i],friendid);
			}
			if(fillbefore){
				$('.msg_container_base').prepend(op);
				can_load_upper_chat=true;
			}else{
				$('.msg_container_base').append(op);
				$('.msg_container_base').scrollTop($('.msg_container_base')[0].scrollHeight);
			}
			
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
function send_msg(msg,friendid){
	if(msg==null||msg==""){
		return false;
	}
	$.post("ajax-req.php",{req_type:"send_msg",'msg':msg,'friendid':friendid}).done(function(d){
		ob=JSON.parse(d);
		if(ob.success==1){
//			op=make_chat_msg__html(ob,friendid);
//			$('.msg_container_base').append(op).scrollTop($('.msg_container_base')[0].scrollHeight);
			$('.chat_input').val('');
		}
		
	});
}
function make_chat_msg__html(ob,friendid){
	
	if(friendid!=$('#current_chat_user_id').val()){//Avoiding loading of chats from diff user inCase though.
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
			"<time>"+ob.time+"</time>" +
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
function get_notification_count(){
	$.post('ajax-req.php',{req_type:'notification_count'}).done(function(d){
		ob=JSON.parse(d);
		$('.notification_count').remove();
		$('#notifications').append(get_notification_count_html(ob.notification_count));
		$('#messages').append(get_notification_count_html(ob.message_count));
	});
	notification_count_timer=setTimeout("get_notification_count()",6000);
}
function get_notification_count_html(c){
	if(c > 0){
		return "<span class='notification_count'>"+c+"</span>";
	}
	return false;
}
$(document).on('click','#friend_action',function(e){
	e.preventDefault();
	$this=$(this);
	
	fid=$this.parents('.card-body-social')[0]?$this.parent().find('button').val():friend_id;
	
	$.post('ajax-req.php',{req_type:'friend_action',friend_id:fid}).done(function(d){
		ob=JSON.parse(d);
		if(ob.success==1){
			$this.find('span').html(ob.new_action);
		}
	});
});