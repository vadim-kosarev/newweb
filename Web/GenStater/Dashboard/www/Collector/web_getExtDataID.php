<?php
if (!array_key_exists("dataTable", $_GET)) {
	echo "no_dataTable";
} else if (!array_key_exists("dStrName", $_GET)) {
	echo "no_dStrName";
	exit(0);
} else {
	include_once("class_event.php");
	$dBase = 10;
	if (array_key_exists("dBase", $_GET)) $dBase = $_GET["dBase"];
	$event = new Event();
	$dataTable = $_GET["dataTable"];
	$dStrName =  $_GET["dStrName"];
	$result = $event->getExtDataID($dataTable, $dStrName, true);
	echo base_convert($result, 10, $dBase);
}
?>
