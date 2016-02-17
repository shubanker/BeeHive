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

define("TEMPLATE", "templates/beehive/");
define("IMAGEDIR", "../Images");
define("IMAGE_SIZE_LIMIT", "2");#Value in Mb

/*
 * List of What possible details can be displayed/Added in about Page.
 */
$About_data_list=array(
		"Mobile",
		"Phone",
		"Occupation",
		"Country",
		"School",
		"High School",
		"College"
);