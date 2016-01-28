  /*==============  Loading Comments ===============*/
  $( document ).on('focus', '.add-comment-input' ,function(d){
	  var postid=$(this).parents('.panel-shadow').find(".postid").val();
	  var comments_list=$(this).parents('.panel-shadow').find(".comments-list");
	  if(comments_list.children().length==0){
		  load_comments(postid,comments_list);
	  }
	});
  $( document ).on('keyup', '.add-comment-input' ,function(d){
	  if(13==d.keyCode){
		  add_coment($(this));
	  }
	});
  $( document ).on('click', '.comment-button' ,function(d){
	  add_coment($(this));
	});
  function add_coment($this){
	  post=$this.parents('.panel-shadow');
	  post_id=post.find(".postid").val();
	  comment=post.find(".add-comment-input").val();
	  post.find(".add-comment-input").val("");
	  $.post("ajax-req.php",
			  {"req_type":"add_comment","post_id":post_id,"comment":comment}).done(function(d){
				  r=JSON.parse(d);
				  if(r.comment_id!=null){
					  ccount=post.find('.c_count');
					  ccount.html(Number(ccount.html())+1);
					  post.find(".comments-list").prepend(make_comment_html(r));
//					  alert(post.find(".comments-list").html());
				  }
			  });
  }
  function make_comment_html(ob){
	  $op='<li class="comment">';
		$op+='<a class="pull-left" href="user.php?id='+ob.user_id+'">';
			$op+='<img class="avatar" src="image.php?user='+ob.user_id+'" alt="avatar"> </a>';
		$op+='<div class="comment-body">';
			$op+='<div class="comment-heading">';
				$op+='<h4 class="comment-user-name"><a href="user.php?id='+ob.user_id+'">'+ob.first_name+' '+ob.last_name+'</a></h4>';
				$op+='<h5 class="time">'+ob.time+'</h5>';
			$op+='</div>';
			$op+='<p>'+ob.comment+'</p>';
		$op+='</div>';
	$op+='</li>';
	return $op;
  } 
  function load_comments(postid,comments_list){
	  $.post("ajax-req.php",{"req_type":"get_comments","post_id":postid}).done(function(data){
			var ob=JSON.parse(data);
			comments_list.empty();
			for(i=0;i<ob.length;i++){
				comments_list.append(make_comment_html(ob[i]));
			}
		});
  }
  
  

  /*============== Likes =======================*/
  function toggle_like(e){
	  lcount=e.find('.count');
	  liked=e.parent().find('.btn-primary')[0];
	  e.toggleClass('btn-default');
	  e.toggleClass('btn-primary');
	  lcount.html(Number(lcount.html())+Number(liked?-1:1));
  }
  function like($this){
	  toggle_like($this);
	  postid=$this.parents('.panel-shadow').find(".postid").val();
	  
	  $.post("ajax-req.php",
			  {"req_type":"toggle_like","post_id":postid,'type':liked?0:1}).done(function(e){
		  r=JSON.parse(e);
		  if(r.success!=1){
			  toggle_like($this);
		  }
	  }).fail(function(e){
		  toggle_like($this);
	  });
  }
//  $('.like').on('click',function(d){
//	  d.preventDefault();
//	  $this=$(this);
//	  like($this);
//	  alert('1');
//  });
  $( document ).on('click', '.like' ,function(d){
	  d.preventDefault();
	  $this=$(this);
	  like($this);
	});

  /*==============  Loading Post ===============*/
  function make_post_html(ob){
	  $op="<div class='panel panel-white post panel-shadow'>\n" +
			"<div class='post-heading'>\n    <div class='pull-left image'>";
		$op+="<img src='image.php?user="+ob.user_id+"&s=s' class='avatar' alt='user profile image'>";
		$op+="</div>"+
			"    <div class='pull-left meta'>"+
			"        <div class='title h5'><a href='user.php?id="+ob.user_id+"' class='post-user-name'>"+ob.first_name+" "+ob.last_name+"</a> "+
		  (ob.picture_id==null?"made a post.":"uploaded a photo.")+"</div>";
		
		$op+="<h6 class='text-muted time'> "+ob.time+"</h6>";
		$op+="</div>"+
			"</div>";
		if(ob.picture_id!=null){
			$op+="<div class='post-image'><img src='image.php?id="+ob.picture_id+"' class='image show-in-modal' alt='image post'>" +
					"</div>";
		}
		$op+="                        <div class='post-description'>"+
			"    <p>"+(ob.post_data==null?"":ob.post_data)+"</p>";
		$op+="<div class='stats'>"+
			"        <a href='#' class='btn "+(ob.has_liked==1?'btn-primary':'btn-default')+" stat-item like'><i class='fa fa-thumbs-up icon'></i><span class='count'>"+ob.like_count+"</span> </a>"+
			"        <a href='#' class='btn btn-default stat-item'><i class='glyphicon glyphicon-comment icon'></i><span class='c_count'>"+ob.comment_count+"</span> </a>"+
			"    </div>"+
			"</div>";
		
		$op+="<div class='post-footer'>"+
			"<div class='input-group'>"+
			"    <input class='form-control add-comment-input' placeholder='Add a comment...' type='text'>" +
			"	<span class='input-group-addon comment-button'><i class='fa fa-edit'></i></span>" +
			"</div>"+
			"    <input class='postid' type='hidden' value='"+ob.post_id+"' />"+
			"    <ul class='comments-list'>"+
			"    </ul>"+
			"</div>"+
			"</div>";
		return $op;
  }
function sync_post(last_sync){
	  $.post("ajax-req.php",{"req_type":"syncpost","last_sync":last_sync}).done(function(d){
		  ob=JSON.parse(d);
		  $op="";
		  for(i=0;i<ob.length;i++){
			    $op+=make_post_html(ob[i]);
		  }
		  $('.post-box-top').after($op);
		  timer=setTimeout("sync_post("+(ob.length>0?ob[0].post_id:last_sync)+")",20000);
	  }).fail(function(){
		  timer=setTimeout("sync_post("+last_sync+")",10000);
	  });
	  
}
/* ========= Making Post ============*/
$(document).on('click','#make_post',function(e){
	e.preventDefault();
	
	var formData = new FormData($('form')[1]);
    $.ajax({
        url: 'ajax-req.php',  //Server script to process data
        type: 'POST',
        xhr: function() {  // Custom XMLHttpRequest
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){ // Check if upload property exists
                myXhr.upload.addEventListener('progress',progressHandlingFunction, false); // For handling the progress of the upload
            }
            return myXhr;
        },
        //Ajax events
        beforeSend: function(){
        	$('#make_post').toggleClass('hidden');
        	$('.progress_bar').toggleClass('hidden');
        },
        success: function(e){
        	$('#make_post').toggleClass('hidden');
        	$('.progress_bar').toggleClass('hidden');
        	ob=JSON.parse(e);
        	if(ob.success==1){
        		$('.post-box-top').after(make_post_html(ob));
        		$('#image_up').val('');
            	$('#post_form').find('textarea').val('');
        	}
        },
        error: function(){

        	$('#make_post').toggleClass('hidden');
        	$('.progress_bar').toggleClass('hidden');
        },
        // Form data
        data: formData,
        //Options to tell jQuery not to process data or worry about content-type.
        cache: false,
        contentType: false,
        processData: false
    });
});
function progressHandlingFunction(e){
    if(e.lengthComputable){
        $('.progress_bar').attr({value:e.loaded,max:e.total});
    }
}
$(document).ready(function() {
	/*==============  Loading Post ===============*/
	  last_sysn=$(".postid").val()==null?0:$(".postid").val();
	  sync_post(last_sysn);
});