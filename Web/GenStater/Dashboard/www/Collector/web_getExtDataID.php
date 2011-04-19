<?php
if (!array_key_exists("dataTable", $_GET)) {
	echo "no_dataTable";
} else if (!array_key_exists("dStrName", $_GET)) {
	echo "no_dStrName";
	exit(0);
} else {
	include_once("class_event.php");
	$event = new Event();
	$dataTable = $_GET["dataTable"];
	$dStrName =  $_GET["dStrName"];
	echo $event->getExtDataID($dataTable, $dStrName, true);
}
?>
