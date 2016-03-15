req_page='ajax-req.php';  
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
	  if(comment==null || comment==""){
		  return false;
	  }
	  post.find(".add-comment-input").val("");
	  $.post(req_page,
			  {"req_type":"add_comment","post_id":post_id,"comment":comment}).done(function(d){
				  r=JSON.parse(d);
				  if(r.comment_id!=null&&r.comment_id){
					  ccount=post.find('.c_count');
					  ccount.html(Number(ccount.html())+1);
					  post.find(".comments-list").prepend(make_comment_html(r));
					  emotify('comment');
				  }
			  });
  }
  function make_comment_html(ob){
	  $op='<li class="comment">';
	  if(ob.can_edit==1){
		  $op+="<button type='button' class='close comment_del' title='Delete Comment' >&times;</button>";
	  }
		$op+='<a class="pull-left" href="profile.php?id='+ob.user_id+'">';
			$op+='<img class="avatar" src="image.php?user='+ob.user_id+'&s=s" alt="avatar"> </a>';
		$op+='<div class="comment-body">';
			$op+='<div class="comment-heading">';
				$op+='<h4 class="comment-user-name"><a href="profile.php?id='+ob.user_id+'">'+(ob.first_name==null?"":ob.first_name)+' '+(ob.last_name==null?"":ob.last_name)+'</a></h4>';
				$op+='<h6 class="text-muted time"><i class="fa fa-clock-o"></i> '+ob.time+'</h6>';
			$op+='</div>';
			$op+='<p>'+ob.comment+'</p>';
			$op+="<input type='hidden' value='"+ob.comment_id+"' class='comment_id'/>";
			$op+="<div class='stats'>" +
					"<a href='#' class='btn "+(ob.has_liked==1?'btn-primary':'btn-default')+" btn-xs stat-item like_comment'><i class='fa fa-thumbs-up icon'></i><span class='count'> "+ob.like_count+" </span> </a>";
				if(ob.can_edit==1){
					$op+="<a href='#' class='btn btn-default btn-xs stat-item edit_comment' data-toggle='modal' data-target='#editPost'> " +
							"<i class='glyphicon glyphicon-edit icon'></i> Edit </a>";
				}
			$op+='</div>';
		$op+='</div>';
	$op+='</li>';
	return $op;
  } 
  function load_comments(postid,comments_list){
	  $.post(req_page,{"req_type":"get_comments","post_id":postid}).done(function(data){
			var ob=JSON.parse(data);
			comments_list.empty();
			for(i=0;i<ob.length;i++){
				comments_list.append(make_comment_html(ob[i]));
			}
			emotify('comment');
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
  function like($this,isComment){
	  if(isComment === undefined){isComment=false}
	  toggle_like($this);
	  if(!isComment){
		  postid=$this.parents('.panel-shadow').find(".postid").val();
		  
		  $.post(req_page,
				  {"req_type":"toggle_like","post_id":postid,'type':liked?0:1}).done(function(e){
			  r=JSON.parse(e);
			  if(r.success!=1){
				  toggle_like($this);
			  }
		  }).fail(function(e){
			  toggle_like($this);
		  });
	  }else{
		  comment=$this.parents('.comment-body');
		  commentid=comment.find(".comment_id").val();
		  $.post(req_page,{req_type:'toggle_comment_like',comment_id:commentid,type:liked?0:1}).done(function(d){
			  r=JSON.parse(e);
			  if(r.success!=1){
				  toggle_like($this);
			  }
		  }).fail(function(e){
			  toggle_like($this);
		  });
		    
	  }
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
$(document).on('click','.like_comment',function(d){
	d.preventDefault();
	$this=$(this);
	like($this,true);
});
  /*==============  Loading Post ===============*/
function manage_postdata_tags(postdata){
	return postdata.replace(/\n/g, "<br>").
	replace(/(#)([\w]{1,10})/ig, '<a href="index.php?hashtag=$2">$&</a>').//Hash tag
	replace(/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/ig, '<a href="$&" target="_blank">$&</a>');//Link
}
  function make_post_html(ob){
	  MAX_LENGTH=250;
	  post_data=ob.post_data==null?"":ob.post_data;
	  seemore=false;
	  seemore_data="";
	  LENGTH=0;
	  if(post_data.length>MAX_LENGTH){
		  seemore=true;
		  len=0;
		  tem=post_data.split(/\s+/);
		  var i;
		  for (i = 0; i < tem.length; i++) {
			len+=tem[i].length;
			if(len>=MAX_LENGTH){
				break;
			}
		}
		  LENGTH=len;
	  }
	  
	  //Limitting Max number of lines
	  lines=post_data.match(/(([\s\S])*?[\n\r]){6}/i);
	  if(lines!=null ){
		  len=lines[0].length;
		  LENGTH=len>LENGTH && LENGTH>0?LENGTH:len;//Checking where we can get shortest.
		  seemore=true;
	  }
	  while(LENGTH>1.3*MAX_LENGTH){//If we still have long length
		  if(tem.length>1){
			  LENGTH-=tem[i--].length;//Lets subtract last variable
		  }else{
			  LENGTH=MAX_LENGTH;
		  }
	  }
	  if(seemore){
		  seemore_data=post_data.slice(LENGTH);
		  post_data=post_data.slice(0,LENGTH);
		  post_data+="<span class='seemore'> ...</span>";
	  }
	  
	  /* Access Icon */
	  switch(ob.access){
	  case "2":
		  title="Friends";
		  access_icon="fa-users";
		  break;
	  case "3":
		  title="Only Me";
		  access_icon="fa-user";
		  break;
	  default:
		  title="Public";
		  access_icon="fa-globe";
	  }
	  access_icon=' <i title="'+title+'" class="post-access fa '+access_icon+'"></i> ';
	  
	  $op="<div class='panel panel-white post panel-shadow'>\n" +
			"<div class='post-heading'>\n    <div class='pull-left image'>";
		$op+="<img src='image.php?user="+ob.user_id+"&s=s' class='avatar' alt='user profile image'>";
		$op+="</div>"+
			"    <div class='pull-left meta'>"+
			"        <div class='title h5'><a href='profile.php?id="+ob.user_id+"' class='post-user-name'>"+(ob.first_name==null?"":ob.first_name)+" "+(ob.last_name==null?"":ob.last_name)+"</a> "+
		  (ob.picture_id==null?"made a post.":"uploaded a photo.")+access_icon+"</div>";
		
		$op+="<h6 class='text-muted time'> <i class='fa fa-clock-o'></i> "+ob.time+"</h6>";
		$op+="</div>";
		if(ob.can_edit==1){
			$op+="<div class='pull-right'>";
			$op+="<div class='dropdown stat-item'>"+
			"  <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenu2' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>"+
			"    <span class='caret'></span>"+
			"  </button>"+
			"  <ul class='dropdown-menu' aria-labelledby='dropdownMenu2'>"+
			"    <li><a href='#' class='edit_post' data-toggle='modal' data-target='#editPost'><i class='glyphicon glyphicon-edit icon'></i> Edit</a></li>";
			if(ob.picture_id!=null){
				$op+="    <li><a href='#' class='make_dp' igm-id='"+ob.picture_id+"'><i class='glyphicon glyphicon-picture icon'></i> Make DP</a></li>";
			}
			$op+="    <li><a href='#' class='del_post'><i class='glyphicon glyphicon-trash icon'></i> Delete</a></li>"+
			"  </ul>"+
			"</div>";
			$op+="</div>";
		}
		$op+="	</div>";
		if(ob.picture_id!=null){
			$op+="<div class='post-image'><img src='image.php?id="+ob.picture_id+"&s=m' class='image show-in-modal' alt='image post'>" +
					"</div>";
		}
		$op+="                        <div class='post-description'>"+
			"    <p>"+manage_postdata_tags(post_data);
		if(seemore){
			$op+="<a href='#' class='seemore'>See More</a>" +
					"<span class='hidden_status hidden'>"+manage_postdata_tags(seemore_data)+"</span>";
		}
		$op+="</p>";
		$op+="<div class='stats'>"+
			"        <a href='#' class='btn "+(ob.has_liked==1?'btn-primary':'btn-default')+" stat-item like'><i class='fa fa-thumbs-up icon'></i><span class='count'>"+ob.like_count+"</span> </a>"+
			"        <a href='#' class='btn btn-default stat-item'><i class='glyphicon glyphicon-comment icon'></i><span class='c_count'>"+ob.comment_count+"</span> </a>";
		$op+="    </div>"+
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
  $(document).on('click','.seemore',function(e){
	    e.preventDefault();
	    $(this).parent().find('.seemore').addClass('hidden');
	    $(this).parent().find('span.hidden_status').removeClass('hidden');
	});
/* Sync Post */
function sync_post(last_sync,toend){

	if(last_sync === undefined){last_sync=null}
	if(toend === undefined){toend=false}
	toend_data=toend?1:0;
//	alert($('.postid').val());
	if(last_sync==null){
		last_sync=$('.postid').val()==null?0:$('.postid').val();
	}
	  $.post(req_page,{"req_type":"syncpost","last_sync":last_sync,from_end:toend_data,friend_id:friend_id}).done(function(d){
//		  alert(d);
		  ob=JSON.parse(d);
		  
		  $op="";
		  for(i=0;i<ob.length;i++){
			    $op+=make_post_html(ob[i]);
		  }
		  if(!toend){
			  $('.post-box-top').after($op);
			  sync_post_timer=setTimeout("sync_post()",8000);
		  }else{
			  $('.profile-info').append($op);
			  if(ob.length>0){
				  ready_to_scroll=true;
			  }
		  }
		  emotify("post");
		  
	  }).fail(function(){
		  if(!toend){
			  sync_post_timer=setTimeout("sync_post()",10000);
		  }
		  
	  });
	  
}
/* Loading Specific Post */
function load_post(postid){
	$.post(req_page,{req_type:"get_post",post_id:postid}).done(function(d){
		ob=JSON.parse(d);
		if(ob.success==1){
			$op=make_post_html(ob[0]);
			$('.post-box-top').after($op);
			emotify("post");
			var comments_list=$('.panel-shadow').find(".comments-list");
			  if(comments_list.children().length==0){
				  load_comments(ob[0].post_id,comments_list);
			  }
		}
	}).fail(function(){
		setTimeout("load_post("+postid+")",4000);
	});
}
/* Loading hashtags Post */
function load_searched_posts(search_key,type){
	req_type="search_in_post";
	switch(type){
		case "hash":
			req="hash";
			break;
		case "inpost":default:
			req="inpost";
			break;
	}
	$.post(req_page,{req_type:req_type,req:req,search_key:search_key}).done(function(d){
		ob=JSON.parse(d);
		$op="";
		  for(i=0;i<ob.length;i++){
			    $op+=make_post_html(ob[i]);
		  }
		  $('.post-box-top').after($op);
		  emotify("post");
	}).fail(function(){
		setTimeout("load_hash_posts("+hash_tag+")",4000);
	});
}
/* ========= Making Post ============*/
$(document).on('click','#make_post',function(e){
	e.preventDefault();
	
	var formData = new FormData($('form')[1]);
    $.ajax({
        url: req_page,  //Server script to process data
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
        	$('.progress_bar').removeClass('hidden');
        	$('#make_post_right').addClass('hidden');
        },
        success: function(e){
        	$('#make_post_right').removeClass('hidden');
        	$('.progress_bar').addClass('hidden');
        	ob=JSON.parse(e);
        	if(ob.success==1){
        		$('.post-box-top').after(make_post_html(ob));
        		emotify("post");
        		$('#image_up').val('');
            	$('#post_form').find('textarea').val('');
        	}
        },
        error: function(){
        	$('#make_post_right').removeClass('hidden');
        	$('.progress_bar').addClass('hidden');
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
/* ========== Modifying post & Comment========= */
function remove_tags(text){
	return text.replace(/<br\s*\/?>/ig, "\n").replace(/<.*?>/g, "");//removing tags.
}
function shrink_text(text,length){
	if(text.length>length){
		text=text.slice(0,length)+"...";
	}
	return text;
}
$(document).on('click','.edit_post',function(){
    post=$(this).parents('.panel-shadow');
    post_data=post.find('.post-description >p').html();
    postid=post.find(".postid").val();
    $('#editPostTextarea').val(remove_tags(post_data));
    $('#editPostId').val(postid);
    $('#editType').val(1);
});
$(document).on('click','.edit_comment',function(){
    comment=$(this).parents('.comment-body');
    post_data=comment.find('p').html();
    postid=comment.find(".comment_id").val();
    $('#editPostTextarea').val(remove_tags(post_data));
    $('#editPostId').val(postid);
    $('#editType').val(2);
});
$(document).on('click','#editPostSubmit',function(){
	editType=$('#editType').val();
	if(editType==1){
		post_id=$('#editPostId').val();
		post_data=$('#editPostTextarea').val();
		$.post(req_page,{req_type:'editpost','post_id':post_id,'post_data':post_data}).done(function(d){
			ob=JSON.parse(d);
			if(ob.success==1){
				$('input.postid[value="'+post_id+'"]').parents('.panel-shadow').find('.post-description >p').html(manage_postdata_tags(post_data));
				emotify('post');
			}
			$('#editPost').modal('hide');
		});
	}else{
		comment_id=$('#editPostId').val();
		comment_data=$('#editPostTextarea').val();
		$.post(req_page,{req_type:'editcomment','comment_id':comment_id,'comment_data':comment_data}).done(function(d){
			ob=JSON.parse(d);
			if(ob.success==1){
				$('input.comment_id[value="'+comment_id+'"]').parents('.comment-body').find('p').html(comment_data);
				emotify('comment');
			}
			$('#editPost').modal('hide');
		});
	}
});
$(document).on('click','.del_post',function(e){
	e.preventDefault();
	if(confirm("Delete This Post ?")){
		$this=$(this);
		$this.addClass('disabled');
		post_id=$this.parents('.panel-shadow').find(".postid").val();
		$.post(req_page,{req_type:'del_post','post_id':post_id}).done(function(d){
			ob=JSON.parse(d);
			
			if(ob.success==1){
				$this.parents('.panel-shadow').fadeOut().remove();
			}else{
				$this.removeClass('disabled');
			}
		}).fail(function(e){
			$this.removeClass('disabled');
		});
	}
});
$(document).on('click','.comment_del',function(e){
	e.preventDefault();
	comment=$(this).parents('.comment');
	commentid=comment.find(".comment_id").val();
	comment_text=remove_tags(comment.find('p').html());
	bootbox.confirm({
		title:"Delete Comment ?",
		message:'Are you shure you Want to Delete this comment ?<br>"<b>'+shrink_text(comment_text,50)+'</b>"',
		buttons:{
			'confirm':{
			      label:'Delete',
			      className:'btn-danger btn'
			    },
			'cancel':{
			      label:'Cancle',
			      className:'btn-default btn'
			    }
		},
		callback:function(result){
			if(result){
				$.post(req_page,{req_type:'del_comment',comment_id:commentid}).done(function(d){
					 ob=JSON.parse(d);
					 if(ob.success==1){
						 ccount = comment.parents('.panel-shadow').find('.c_count');
						 ccount.html(Number(ccount.html())-1);
						 comment.fadeOut().remove();
					 }
				 });
			}
		}
	});
	 
});

/* ========== Scroll ==========*/
$(document).on('scroll',function(){
	if($(this).innerHeight()-$(this).scrollTop()<1200){
		if(ready_to_scroll){
			ready_to_scroll=false;
			last_id=$('.post-footer').last().find(".postid").val();
			sync_post(last_id,true);
		}
	}
});
/* ========== Load Friends_list ======*/
function load_friend_list(friend_id,limit){
	$.post(req_page,{req_type:'get_friend_list',friend_id:friend_id,limit:limit}).done(function(d){
//		alert(d);
		ob=JSON.parse(d);
		for (var i = 0; i < ob.length; i++) {
			$('.friends').append(make_friend_html(ob[i]));
		}
		$('.tip').tooltip();
	});
}
function make_friend_html(ob){
	$op="<li>\n" +
			"<a href='profile.php?id="+ob.user_id+"'>\n" +
					"<img data-original-title='"+ob.name+"' src='image.php?user="+ob.user_id+"&s=s' title='"+ob.name+"' class='img-responsive tip'> </a>\n" +
			"</li>";
	return $op;
}

/* ========== Load Images ======*/
function load_image_list(friend_id,limit){
	$.post(req_page,{req_type:'get_Images',friend_id:friend_id,limit:limit}).done(function(d){
//		alert(d);
		ob=JSON.parse(d);
		for (var i = 0; i < ob.length; i++) {
			$('.photos').append(make_image_html(ob[i]));
		}
	});
}
function make_image_html(ob){
	$op="<li>"+
	"<a href='#'><img src='image.php?s=ms&id="+ob.picture_id+"' alt='photo 1' class='img-responsive show-in-modal'> </a>"+
	"</li>";
	return $op;
}
/* Change Dp */
$(document).on('click','.make_dp',function(e){
	e.preventDefault();
	img_id=$(this).attr('igm-id');
	$.post(req_page,{req_type:'change_dp',img_id:img_id}).done(function(d){
		ob=JSON.parse(d);
		if(ob.success==1){
			$('.img-user').attr('src',$('.img-user').attr('src')+"&rand="+Math.random());
		}
	});
});