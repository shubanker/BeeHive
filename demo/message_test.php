<?php
require_once '../_includes/include_all.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);
/*
 * All the functions are static no need to create any object
 * just call them along with its class name
 */

/*
 * Sending message
 * 1st one is the sender and 2nd one is the receiver
 * 3rd is the message and 4th argument is database object.
 */

// echo message::send_message(1, 8, "Or bhai kaisa hai?", $db);
// echo message::send_message(8, 1, "Maze me hu Apna suna", $db);

/*
 * for getting recent messages between 2 users.
 * there are more arguments than the 3 shown below useful to limit no of messages(pagination)
 * 
 * last argument $after is useful in AJAX call for getting messages after a perticular message id.
 */
echo Message::get_messages($user_one, $user_two, $db);

// Message::mark_read(2, $db);
// Message::mark_received($message_id, $db)

/*
 * Function returns list of recent users contacted with along with last message
 * useful for displaying in inbox page.
 * 
 */
print_r(Message::get_recent_message_list(3, $db));
// print_r(Message::get_messages(1, 3, $db,null,null,3));
$db->close();