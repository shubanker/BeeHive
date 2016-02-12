$(document).ready(function() {
	refresh_recent_messages_list();
	start_sync=true;
});
function refresh_recent_messages_list(){
	$.post('ajax-req.php',{req_type:"messages_list"}).done(function(d){
//		alert(d);
		ob=JSON.parse(d);
		op="";
		for (var i = 0; i < ob.length; i++) {
			op+=make_recent_messages_html(ob[i]);
		}
		$(".friend-list").html(op);
		
		if(start_sync){
			start_sync=false;
			if(friend_id==0){
				first_msg=$('.friend-list>li');
				$('#current_msg_user_id').val(first_msg.find('input').val());
				$('#current_msg_user_name').val(first_msg.find('strong').html());
			}else{
				$('#current_msg_user_id').val(friend_id);
			}
			load_msg();
		}
	});
	setTimeout('refresh_recent_messages_list()',7000);
}
function make_recent_messages_html(ob){
	name=(ob.first_name==null?"":ob.first_name)+" "+(ob.last_name==null?"":ob.last_name);
	
	$op="<li class='"+(ob.user_one==ob.user_id && ob.status==1?"active bounceInDown":"")+"'>"+
	"<a href='#' class='clearfix userleft'><img src='image.php?user="+ob.user_id+"&s=s' alt='"+name+"' class='img-circle'>"+
	"<div class='friend-name'><strong> "+name+"</strong>"+
	"<input type='hidden' value='"+ob.user_id+"'/>"+
	"</div>"+
	"<div class='last-message text-muted'> "+ob.message+"</div><small class='time text-muted'> "+ob.time+" </small><small class='chat-alert text-muted'><i class='fa  "+(ob.user_id==ob.user_one?" fa-mail-forward":(ob.status>1?"fa-check":"fa-send"))+"'></i></small>"+
	"</a>"+
	"</li>";
	return $op;
}
/* ============= Messages ============== */
msg_timer=0;
can_load_upper_msg=true;
function load_msg(lastsync){
	if(lastsync === undefined){lastsync=0}
	$('.chat').html('');
	sync_messages(lastsync);
}
function sync_messages(lastsync,fillbefore){
	if(lastsync === undefined){last_sync=null}
	if(fillbefore === undefined){fillbefore=false}
	if(lastsync==null){
		if(!fillbefore){
			lastsync=$('.chat').find('input').last().val();
		}else{
			lastsync=$('.chat').find('input').val();
		}
		lastsync=(lastsync==null?0:lastsync);
	}
	friendid=$('#current_msg_user_id').val();
	if(friendid==null || friendid==''){
		return false;
	}
	$.post("ajax-req.php",{req_type:"get_msg",'lastsync':lastsync,'friendid':friendid,'fillbefore':(fillbefore?0:1)}).done(function(d){
		ob=JSON.parse(d);
		if(ob.length>0){
			lastsync=ob[ob.length-1].message_id;
			op="";
			for (var i = 0; i < ob.length; i++) {
				op+=make_msg_html(ob[i],friendid);
			}
			
			if(fillbefore){
				$('.chat').prepend(op);
				can_load_upper_msg=true;
			}else{
				$('.chat').append(op);
				$('.chat').scrollTop($('.chat')[0].scrollHeight);
			}
			
			
		}
		msg_timer=setTimeout("sync_messages()",2000);
	});
}
$('.chat').on('scroll',function(){
	if($(this).scrollTop()<150 && can_load_upper_msg){
		can_load_upper_msg=false;
		friendid=$('#current_msg_user_id').val();
		sync_messages(null,true);
	}
});
function make_msg_html(ob,friendid){
	if(friendid!=$('#current_msg_user_id').val()){//Avoiding loading of chats from diff user inCase though.
		return '';
	}
	isreceived=ob.user_one==friendid;
	direction=isreceived?"left":"right";
	name=isreceived?$('#current_msg_user_name').val():"You";
	
	$op="<li class='"+direction+" clearfix'><span class='chat-img pull-"+direction+"'><img src='image.php?user="+ob.user_one+"&s=s' alt='"+name+"'> </span>"+
	"<div class='chat-body clearfix'>"+
	"<div class='header'><strong class='primary-font'>"+name+"</strong> <small class='pull-right text-muted'><i class='fa fa-clock-o'></i>"+ob.time+"</small>"+
	"</div>"+
	"<p>"+ob.message+"</p>";
	if(!isreceived){
		icon=ob.status>1?"fa-check":"fa-send"
		$op+="<small class='pull-right chat-alert text-muted'><i class='fa "+icon+"'></i></small>";
	}
	$op+="<input type='hidden' value='"+ob.message_id+"'/>"+
	"</div>"+
	"</li>";
	return $op;
}
$(document).on('click','.userleft',function(e){
	e.preventDefault();
	can_load_upper_msg=true;
	$('#current_msg_user_id').val($(this).find('input').val());
	$('#current_msg_user_name').val($(this).find('strong').html());
	
	clearTimeout(msg_timer);
	msg_timer=0;
	
	load_msg();
});
$(document).on('click','#send_msg',function(e){
	e.preventDefault();
	firendid=$('#current_chat_user_id').val();
	msg=$('#message_textarea').val();
	if(msg!=null && msg!=""){
		send_msg(msg,friendid);
	}
});