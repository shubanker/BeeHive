<?php

require_once '../_includes/include_all.php';

$db=new Db(DBUSER, DBPASSWORD, DATABASE);
/*
 * Creating a new Post.
 */
$post=new Post();
// $post->set_user_id(8);
// $post->set_post_data("Lorem ipsum dolor sit amet, architecto voluptatibus impedit!");
	/*
	 * These are optional
	 */
// 	$post->set_link($link);
// 	$post->set_picture_id($picture_id);
// 	$post->set_access($access);
	/*
	 * And this shoudn't be used while creating post
	 * they are automatically handled yet exist just in case.
	 * 
	 */
// 	$post->set_status($status);
// 	$post->set_time($time);
// $post->create($db);

/*
 * Reading a post
 */
// $post=new Post(7,$db);
// print_r($post->get_data());//getting all data of the post(time,status,id etc)

// echo $post->get_link();
// echo $post->get_post_data();//Getting status text and others exist just check the class for function names


// $post->delete($db);

/*
 * Liking a post by a user
 * 1st argument is user id
 * 2nd is post id.
 */
// Post::like_post(1, 1, $db);
// Post::like_post(8, 1, $db);

// Post::unlike_post(1, 1, $db);

// echo Post::count_post_likes(1, $db);
// print_r(Post::get_post_likers(1, $db));

/*
 * Comments
 */
// echo Post::add_comment(1, 1, "Good one'", $db);
// print_r(Post::get_post_comments(1, $db));
// echo Post::edit_comment(3, "Great!!", $db);

/*
 * Getting all recent post's for homepage
 */
// print_r(Feeds::get_feeds(8, $db));
/*
 * Getting a friends recent satus(viewing a users profile)
 */
// print_r(Feeds::get_friends_feeds(3, 1, $db));

$db->close();