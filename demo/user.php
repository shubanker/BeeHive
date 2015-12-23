<?php
require_once '../_includes/include_all.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);
/*
 * To create a new user
 */

// $user=new User();

// $user->set_first_name("Subhanker");
// $user->set_last_name("Chourasia");
// $user->set_dob("25-APR-1994");
// $user->set_status(1);
// $user->set_password("password");
// echo $user->create($db);//To create user in database and will return user id.

 /*
  * to get user data
  */
$user=new User(1,$db);
$user->set_dob("25-APR-1994");
echo $user->get_gen();
$user->update($db);