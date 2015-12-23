<?php
require_once '../_includes/include_all.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);
/*
 * To create a new user
 */

$user=new User();

$user->set_first_name("Aamir");
$user->set_last_name("Sohail");
$user->set_email("aamirsohail@gmail.com");
$user->set_dob("03-AUG-1994");
$user->set_gender("M");
$user->set_status(1);
$user->set_password("password");
echo $user->create($db);//To create user in database and will return user id.

 /*
  * to get user data
  */
// $user=new User(1,$db);
// $user->set_dob("25-APR-1994");
// $user->set_email("subhankerchourasia@gmail.com");
// echo $user->get_gen();
// $user->update($db);