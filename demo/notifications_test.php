<?php

require_once '../_includes/include_all.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);

/*
 * Notifications are mainly handled automattically usig triggers 
 * yet this function has been Added for JUST IN CASE :) (refer to function for arguments details)
 */
// echo Notifications::add_notification($user_id, $post_id, $type);
// echo Notifications::add_notification(1, 1, 1, null,null);

Notifications::mark_read($notification_id, $db);
Notifications::mark_unread($notification_id, $db);

Notifications::get_notification_count($user_id,$db);
/*
 * Returns array of notifications and notification id
 */
Notifications::get_notifications($user_id,$db);

print_r(Notifications::get_notifications(1,null,$db));