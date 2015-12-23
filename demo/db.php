<?php
require_once '../_includes/include_all.php';
$whereData=array(
		"name"=>"subhanker",
		"gen"=>"male",
		"skills"=>array(
				"java",
				"php",
				"mysql",
				"sqlite"
		),
		"type"=>"cool"
);
echo Db::create_sql_delete('test', $whereData);
