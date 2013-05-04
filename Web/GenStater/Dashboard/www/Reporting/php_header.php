<?php 
if (array_key_exists("configSuffix", $_GET)) {
	include_once("../Common/sys_config_" . $_GET["configSuffix"] . ".php");
} else {
	include_once("../Common/sys_config.php");
}
include_once("../Common/sys_utils.php");
?>