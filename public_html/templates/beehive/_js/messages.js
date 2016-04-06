$(document).ready(function() {
	refresh_recent_messages_list();
	start_sync=true;
});
function refresh_recent_messages_list(){
	$.post('ajax-req.php',{req_type:"messages_list",access_key:access_key}).done(function(d){
//		alert(d);
		ob=JSON.parse(d);
		op="";
		for (var i = 0; i < ob.length; i++) {
			op+=make_recent_messages_html(ob[i]);
		}
		if(ob.length>0){
			$(".friend-list").html(op);
		}
		
		
		if(start_sync){
			start_sync=false;
			if(friend_id==0){
				first_msg=$('.friend-list>li');
				$('#current_msg_user_id').val(first_msg.find('input').val());
				$('#current_msg_user_name').val(first_msg.find('strong').attr('fullname'));
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

	shortname=name.length>10?(name.substr(0,10)+"..."):name;//Making Name shorter if too long..
	
	$op="<li class='"+(ob.user_one==ob.user_id && ob.status==1?"active bounceInDown":"")+"'>"+
	"<a href='#' class='clearfix userleft'><img src='image.php?user="+ob.user_id+"&s=s' alt='"+name+"' class='img-circle'>"+
	"<div class='friend-name'><strong fullname='"+name+"'> "+shortname+"</strong>"+
	"<input type='hidden' value='"+ob.user_id+"'/>"+
	"</div>"+
	"<div class='last-message text-muted'> "+ob.message+"</div><small class='time text-muted' title='"+ob.full_time+"'> "+ob.time+" </small><small class='chat-alert text-muted'><i class='fa  "+(ob.user_id==ob.user_one?" fa-mail-forward":(ob.status>1?"fa-check":"fa-send"))+"'></i></small>"+
	"</a>"+
	"</li>";
	return $op;
}
/* ============= Messages ============== */
msg_timer=0;
can_load_upper_msg=true;
function load_msg(lastsync){
	if(lastsync === undefined){lastsync=0}
	can_load_upper_msg=true;
	friendid=$('#current_msg_user_id').val();
	cache_msg='';
	/* populating with cached data */
	if(msg_data[friendid] !== undefined ){
		for (i in msg_data[friendid]) {
			cache_msg+=make_msg_html(msg_data[friendid][i],friendid);
		}
	}
	$('.chat').html(cache_msg);
	$('.chat').scrollTop($('.chat')[0].scrollHeight);
	emotify('message');
	sync_messages();
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
	$.post("ajax-req.php",{req_type:"get_msg",'lastsync':lastsync,'friendid':friendid,'fillbefore':(fillbefore?0:1),access_key:access_key}).done(function(d){
		ob=JSON.parse(d);
		if(ob.length>0){
			lastsync=ob[ob.length-1].message_id;
			op="";
			if(msg_data[friendid] === undefined ){msg_data[friendid]={};}//initialising cache for friend id
			for (var i = 0; i < ob.length; i++) {
				msg_data[friendid][ob[i].message_id]=ob[i];//saving for cache data.
				op+=make_msg_html(ob[i],friendid);
			}
			
			if(fillbefore){
				$('.chat').prepend(op);
				can_load_upper_msg=true;
			}else{
				$('.chat').append(op);
				$('.chat').scrollTop($('.chat')[0].scrollHeight);
			}
			emotify('message');
			
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
	if($('.chat-body>input[value="'+ob.message_id+'"]').length>0){//Avoiding repetation of messages..
		return '';
	}
	isreceived=ob.user_one==friendid;
	direction=isreceived?"left":"right";
	name=isreceived?$('#current_msg_user_name').val():"You";
	
	$op="<li class='"+direction+" clearfix'><span class='chat-img pull-"+direction+"'><img src='image.php?user="+ob.user_one+"&s=s' alt='"+name+"'> </span>"+
	"<div class='chat-body clearfix'>"+
	"<div class='header'><a class='primary-font' href='profile.php?id="+ob.user_one+"'>"+name+"</a> <small class='pull-right text-muted' title='"+ob.full_time+"'><i class='fa fa-clock-o'></i>"+ob.time+"</small>"+
	"</div>"+
	"<p>"+ob.message+"</p>";
	if(!isreceived){
		icon=ob.status> 1 ? "fa-check": (ob.status == -1 ? "fa-clock-o" : "fa-send");
		$op+="<small class='pull-right chat-alert text-muted'><i class='fa "+icon+"'></i></small>";
	}
	$op+="<input type='hidden' value='"+ob.message_id+"'/>"+
	"</div>"+
	"</li>";
	return $op;
}

/*
 * When left pannel is clicked..
 */
$(document).on('click','.userleft',function(e){
	e.preventDefault();
	can_load_upper_msg=true;
	$('#current_msg_user_id').val($(this).find('input').val());
	$('#current_msg_user_name').val($(this).find('strong').attr('fullname'));
	
	clearTimeout(msg_timer);
	msg_timer=0;
	
	//making textarea to focus.
	$('#message_textarea').focus();
	
	load_msg();
});
send_message_function=function(e){
	e.preventDefault();
	firendid=$('#current_chat_user_id').val();
	msg=$('#message_textarea').val();
	if(msg!=null && msg!=""){
		send_msg(msg,friendid,true);
	}
};
$(document).on('click','#send_msg',send_message_function);
$( document ).on('keyup', '#message_textarea' ,function(d){
	  if(13==d.keyCode && !d.shiftKey && $('#enter_to_send').is(':checked')){
		  send_message_function(d);
	  }
	});