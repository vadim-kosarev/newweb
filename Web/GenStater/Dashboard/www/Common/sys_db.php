<?php
    include_once("sys_config.php");
	$dbUrl = "mysql:host=$config_db_host;dbname=$config_db_name;port=$config_db_port";
	$dbh = new PDO($dbUrl, $config_db_user, $config_db_password);
?>