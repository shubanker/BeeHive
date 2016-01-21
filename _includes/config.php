<?php

if (! ob_start ( "ob_gzhandler" )){
	ob_start (); // Enabeling Gzip.
}
session_start ();
date_default_timezone_set("Asia/Kolkata");


/*
 * Database details
 */

define('DATABASE', 'social');
define('DBUSER', 'beehive');
define('DBPASSWORD', 'beehive');
define('DBHOST', 'localhost');

define("TEMPLATE", "templates/cn/");