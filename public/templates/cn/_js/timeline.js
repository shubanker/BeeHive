  /*==============  Loading Comments ===============*/
  $( document ).on('focus', '.add-comment-input' ,function(d){
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
							$op+='<h5 class="time">'+ob[i].time+'</h5>';
						$op+='</div>';
						$op+='<p>'+ob[i].comment+'</p>';
					$op+='</div>';
				$op+='</li>';
				comments_list.append($op);
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
	  
	  $.post("ajax-req.php",{"req_type":"toggle_like","post_id":postid,'type':liked?0:1}).done(function(e){
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
function sync_post(last_sync){
	  $.post("ajax-req.php",{"req_type":"syncpost","last_sync":last_sync}).done(function(d){
		  ob=JSON.parse(d);
		  $op="";
		  for(i=0;i<ob.length;i++){
			    $op+="<div class='panel panel-white post panel-shadow'>\n" +
			    		"<div class='post-heading'>\n    <div class='pull-left image'>";
			    $op+="<img src='image.php?user="+ob[i].user_id+"&s=s' class='avatar' alt='user profile image'>";
			    $op+="</div>"+
			    "    <div class='pull-left meta'>"+
			    "        <div class='title h5'><a href='user.php?id="+ob[i].user_id+"' class='post-user-name'>"+ob[i].first_name+" "+ob[i].last_name+"</a> "+
			        (ob[i].picture_id==null?"made a post.":"uploaded a photo.")+"</div>";
			    $op+="<h6 class='text-muted time'> "+ob[i].time+"</h6>";
			    $op+="</div>"+
			    "</div>";
			    if(ob[i].picture_id!=null){
			    	$op+="<div class='post-image'><img src='image.php?id="+ob[i].picture_id+"' class='image show-in-modal' alt='image post'>";
			    }
			    $op+="                        <div class='post-description'>"+
			    "    <p>"+ob[i].post_data+"</p>";
			    $op+="<div class='stats'>"+
			    "        <a href='#' class='btn "+(ob[i].has_liked==1?'btn-primary':'btn-default')+" stat-item like'><i class='fa fa-thumbs-up icon'></i><span class='count'>"+ob[i].like_count+"</span> </a>"+
			    "        <a href='#' class='btn btn-default stat-item'><i class='glyphicon glyphicon-comment icon'></i>"+ob[i].comment_count+"</a>"+
			    "    </div>"+
			    "</div>";
			    
			    $op+="<div class='post-footer'>"+
			    "    <input class='form-control add-comment-input' placeholder='Add a comment...' type='text'>"+
			    "    <input class='postid' type='hidden' value='"+ob[i].post_id+"' />"+
			    "    <ul class='comments-list'>"+
			    "       <!-- Will load through AJAX -->"+
			    "    </ul>"+
			    "</div>"+
			    "</div>";
		  }
		  $('.post-box-top').after($op);
		  timer=setTimeout("sync_post("+(ob.length>0?ob[0].post_id:last_sync)+")",1000);
	  });
}