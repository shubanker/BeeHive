<?php
require_once '../_includes/include_all.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);
/*
 * To create a new user ..
 */

/*
 * Creating a new user object
 */
$user=new User();
/*
 * Adding Details
 */
// $user->set_first_name("Aamir");
// $user->set_last_name("Sohail");
// $user->set_email("aamirsohail@gmail.com");
// $user->set_dob("03-AUG-1994");
// $user->set_gender("M");
// $user->set_status(1);
// $user->set_password("password");
/*
 * Creating a user
 * function create returns new user_id if success else false if failed due to any reason.
 */
// echo $user->create($db);//To create user in database and will return user id.

 /*
  * to get user data
  */
//Passing user_id and databse object into user constructor.
// $user=new User(1,$db);
// echo $user->get_gen();
// echo $user->get_name();

/*
 * Updating data
 */

// $user->set_dob("25-APR-1994");
// $user->set_email("subhankerchourasia@gmail.com");
// $user->update($db);


/*
 * Managing User Data.
 * kindly refer to functions names and its arguments names for explanation they are self explanatory
 */
// echo UserData::add_data(1, 'School', 'Carmel School', $db);
// echo UserData::add_data(1, 'High School', 'B.N.S DAV Public School', $db);
// echo UserData::edit_data(2, null, "B.N.Saha DAV Public School", $db);
// print_r(UserData::edit_data($data_id, $type, $data, $db));
// print_r(UserData::get_all_data(1, $db));
// UserData::remove_data(1, $db);

// UserData::remove_data($data_id, $db);
// UserData::re_activate_data(1, $db);
print_r(User::search_users_by_name("s", $db));
$db->close();