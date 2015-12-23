<?php
require_once '../_includes/include_all.php';
$db=new Db(DBUSER, DBPASSWORD, DATABASE);
echo Friendship::get_relation(1, 3, $db);
print_r (Friendship::get_friends_names(1, $db));
print_r(Friendship::get_mutuals(1, 3, $db));