$(document).ready(function() {
   /*============ Chat sidebar ========*/
//  $('.chat-sidebar, .nav-controller, .chat-sidebar a').on('click', function(event) {
//      $('.chat-sidebar').toggleClass('focus');
//  });

  $(".hide-chat").click(function(){
      $('.chat-sidebar').toggleClass('focus');
  });

  $('.tip').tooltip();
  
  /*show image in modal when click*/
  $('.show-in-modal').click(function(e){
    $('#modal-show .img-content').html('<img class="img-responsive img-rounded" src="'+$(this).attr('src')+'" />');
    $('#modal-show').modal('show');
    e.preventDefault();
  });

  /*chat box*/
  $(document).on('click', '.chat-sidebar .list-group .list-group-item', function (e) {
	  e.preventDefault();
	  //Chatbox user name
	  name=$(this).find('.chat-user-name').html();
	  friendid=$(this).find('input').val();
	  
	  $('#chat_name').html(name);
	  $('.chat-window').show();
	  $('#current_chat_user_id').val(friendid);
	  load_chat(friendid);
  });

  $(document).on('click', '.icon_close', function (e) {
    $(this).closest('.chat-window').hide();
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
	load_online_list();
});
/* ============= Online users ================= */
function load_online_list(){
	$.post("ajax-req.php",{req_type:"online_list"}).done(function(d){
		ob=JSON.parse(d);
		$op="";
		for (var i = 0; i < ob.length; i++) {
			$op+=make_chat_html(ob[i]);
		}
		$(".chat_list").html($op);
	});
	setTimeout('load_online_list()',15000);
}
function make_chat_html(ob){
	$op="<a href='#' class='list-group-item'><i class='fa ";
		$op+=ob.data>200?"fa-times-circle absent-status":"fa-check-circle connected-status";
	$op+="'></i>";
	$op+="<input type='hidden' value='"+ob.user_id+"'/>";
	$op+="<img src='image.php?user="+ob.user_id+"&s=s' class='img-chat img-thumbnail'> <span class='chat-user-name'>";
	$op+=ob.first_name+" "+ob.last_name;
	$op+="</span> </a>";
	return $op;
}
function load_chat(friendid,lastsync=0){
	$.post("ajax-req.php",{req_type:"get_msg",'lastsync':lastsync,'friendid':friendid}).done(function(d){
		ob=JSON.parse(d);
		op="";
		for (var i = 0; i < ob.length; i++) {
			op+=make_chat_msg__html(ob[i],friendid);
		}
		$('.msg_container_base').html(op);
		$('.msg_container_base').scrollTop($('.msg_container_base')[0].scrollHeight);
	});
}
function make_chat_msg__html(ob,friendid){
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
					"</div>" +
				"</div>";
	if(!isreceived){
		op+=image;
	}
	op+="</div>";
	return op;
}