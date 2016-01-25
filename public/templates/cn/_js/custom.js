$(document).ready(function() {
   /*============ Chat sidebar ========*/
  $('.chat-sidebar, .nav-controller, .chat-sidebar a').on('click', function(event) {
      $('.chat-sidebar').toggleClass('focus');
  });

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
    $('.chat-window').show();
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
  $("#statusbox").on('focus',function(){
	  $("#statusboxfooter").removeClass("hidden");
  });
  $('.add-comment-input').on('focus',function(){
	  var postid=$(this).parent().find(".postid").val();
	  var comments_list=$(this).parent().find(".comments-list");
	  if(comments_list.children().length==0){
		  load_comments(postid,comments_list);
	  }
	  
  });
  function load_comments(postid,comments_list){
	  $.post("ajax-req.php",{"req_type":"get_comments","post_id":postid}).done(function(data){
			var ob=JSON.parse(data);
			comments_list.empty();
			for(i=0;i<ob.length;i++){
				$op='<li class="comment">';
				$op+='<a class="pull-left" href="user.php?id='+ob[i].user_id+'">';
				$op+='<img class="avatar" src="image.php?user='+ob[i].user_id+'" alt="avatar"> </a>';
				$op+='<div class="comment-body">';
				$op+='<div class="comment-heading">';
				$op+='<h4 class="comment-user-name"><a href="user.php?id='+ob[i].user_id+'">'+ob[i].first_name+' '+ob[i].last_name+'</a></h4>';
				$op+='<h5 class="time">'+ob[i].time+'</h5></div>';
				$op+='<p>'+ob[i].comment+'</p> </div></li>';
				comments_list.append($op);
			}
		});
  }
})