$(document).ready(function() {
	  load_notifications(0,true);
});
function make_notifications_html(ob){
	var isPost=ob.post_id != null;
	$op="<div class='panel panel-white post panel-shadow no-overflow"+(ob.status==1?"grey-bg":"")+"'>"+
	"    <div class='post-heading'>";
	if((isPost && null != ob.post_img)|| null != ob.from_user_id){
		image_src="image.php?s=s&"+(null != ob.from_user_id?"user="+ ob.from_user_id:"id="+ob.post_img);
		$op+="<div class='pull-left image'>" +
			"<img src='"+image_src+"' class='avatar' alt='image'>" +
			"</div>";
		post_type="Image";
	}else{
		post_type="Post";
	}
	var message_prefix="";
	if(null != ob.from_user_id){
		message_prefix="<a href='profile.php?id="+ob.from_user_id+"' title='"+ob.from_user_name+"' class='user_name'>"+ob.from_user_name+" </a> ";
	}else if(null != ob.people_count){
		message_prefix = ob.people_count+" ";
	}
	
	$op+="        <div class='pull-left meta'>"+
	"        <div class='title h5'> "+message_prefix+ob.message+" ";
	if(isPost){
		$op+="        <a href='index.php?post="+ob.post_id+"'>"+post_type+"</a>";
	}
	$op+="        <h6 class='text-muted time notification-time' title='"+ob.full_time+"'><i class='fa fa-clock-o'></i> "+ob.time+"</h6>"+
	"        </div>";
	if(isPost){
		$op+=(ob.post_data == null?"":'        <h6>"'+ob.post_data+'..."<h6>');
	}else if(ob.type==3){
		$op+='<h6><button type="button" class="btn btn-success btn-xs" id="friend_action_notification" value="'+ob.from_user_id+'"><i class="glyphicon glyphicon-user"></i> <span>Accept Request</span></button></h6>';
	}
	$op+="        </div>"+
	"        <input type='hidden' value='"+ob.notification_id+"'>"+
	"    </div>"+
	"</div>"
	return $op;
}
/* ========== Load Notifications ======*/
function load_notifications(lastsync,add_at){
	if(add_at === undefined){add_at=null}
	$.post('ajax-req.php',{req_type:'get_notifications',lastsync:lastsync,access_key:access_key}).done(function(d){
		ob=JSON.parse(d);
		op="";
		for (var i = 0; i <ob.length ; i++) {
			op+=make_notifications_html(ob[i]);
		}
		if(add_at==null){
			$('.notifications').html(op);
		}else{
			if(add_at){
				$('.notifications').append(op);
				ready_to_scroll=true;
			}else{
				$('.notifications').prepend(op);
			}
		}
	});
}
/* ========== Scroll ==========*/
var ready_to_scroll=true;
$(document).on('scroll',function(){
	if($(this).innerHeight()-$(this).scrollTop()<1200){
		if(ready_to_scroll){
			ready_to_scroll=false;
			last_id=$('.post-heading').last().find("input").val();
			load_notifications(last_id,true);
		}
	}
});