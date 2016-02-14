$(document).ready(function() {
	  load_notifications(0,true);
});
function make_notifications_html(ob){
	$op="<div class='panel panel-white post panel-shadow "+(ob.status==1?"grey-bg":"")+"'>"+
	"    <div class='post-heading'>"+
	"        <div class='pull-left meta'>"+
	"        <div class='title h5'> "+ob.message+" <a href='post.php?id="+ob.post_id+"'>Post</a>"+
	"        </div>"+
	"        <h6 class='text-muted time'>"+ob.time+"</h6>"+
	"        </div>"+
	"        <input type='hidden' value='"+ob.notification_id+"'>"+
	"    </div>"+
	"</div>"
	return $op;
}
/* ========== Load Notifications ======*/
function load_notifications(lastsync,add_at){
	if(add_at === undefined){add_at=null}
	$.post('ajax-req.php',{req_type:'get_notifications',lastsync:lastsync}).done(function(d){
		ob=JSON.parse(d);
		op="";
		for (var i = ob.length-1; i >=0 ; i--) {
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